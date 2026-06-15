<?php

namespace app\models;
use Flight;
use PDO;

abstract class Model
{
    protected static string $table = "";
    protected static string $primaryKey = "id";
    protected static array $hidden = [];
    protected array $attributes = [];
    protected array $dirty = [];
    protected bool $exists = false;

    protected static bool $timestamps = true;
    protected array $instanceWheres = [];
    private ?int $instanceLimits = null;
    private ?array $instanceOrders = null;

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    public function fill(array $attributes): static
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }

        return $this;
    }

    public function toArray(): array
    {
        return array_diff_key($this->attributes, array_flip(static::$hidden));
    }

    public function __get(string $key)
    {
        return $this->attributes[$key] ?? 0;
    }

    public function __set(string $key, mixed $value)
    {
        $this->attributes[$key] = $value;
        $this->dirty[$key] = $value;
    }

    public function __isset(string $key)
    {
        return isset($this->attributes[$key]);
    }

    protected static function db()
    {
        return Flight::db();
    }

    // Funções de banco
    public static function where(string $column, string $op, mixed $value = null): static 
    {
        if ($value === null) {
            $value = $op;
            $op = '=';
        }
        $instance = new static();
        $instance->pushWhere($column, $op, $value);
        return $instance;
    }

    public function andWhere(string $col, string $op, mixed $val = null): static {
        if ($val === null) { $val = $op; $op = '='; }
        $this->pushWhere($col, $op, $val);
        return $this;
    }

    private function pushWhere(string $col, string $op, mixed $val): void
    {
        $this->instanceWheres[] = compact('col', 'op', 'val');
    }

    public function orderBy(string $col, string $level): static {
        $this->instanceOrders[] = "$col $level";
        return $this;
    }

    public function limit(int $limit): static {
        $this->instanceLimits = $limit;
        return $this;
    }

    private function queryBuilder(string $select = '*') {
        $table = static::$table;
        $sql = "SELECT $select FROM $table";
        $params = [];

        $wheres = $this->instanceWheres;
        if (!empty($wheres)) {
            $clauses = [];
            foreach ($wheres as $w) {
                if ($w['val'] === null) {
                    $clauses[] = "{$w['col']} {$w['op']} NULL";
                } else {
                    $clauses[] = "{$w['col']} {$w['op']} ?";
                    $params[] = $w['val']; 
                }
            }
            $sql .= " WHERE " . implode(" AND ", $clauses);
        }

        if (!empty($this->instanceOrders)) {
            $sql .= " ORDER BY " . implode(", ", $this->instanceOrders);
        }

        if (!empty($this->instanceLimits)) {
            $sql .= " LIMIT " . $this->instanceLimits;
        }

        return [$sql, $params];
    }

    public function get(): array
    {
        [$sql, $params] = $this->queryBuilder();
        $stmt = static::db()->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        return array_map(function ($row) {
            $obj = new static($row);
            return $obj;
        }, $rows);
    }

    public function first(): ?static
    {
        $this->instanceLimits = 1;
        $results = $this->get();
        return $results[0] ?? null;
    }

    public static function find(int $id): static
    {
        return static::where(static::$primaryKey,"=", $id)->first();
    }

    public static function all(): array {
        return (new static)->get();
    }

    public function count(): int {
        [$sql, $params] = $this->queryBuilder("COUNT(*) as aggregate");
        $stmt = static::db()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function save(): bool
    {
        if (static::$timestamps) {
            $now = date("Y-m-d H:i:s");
            if (!$this->exists) {
                $this->attributes['created_at'] = $now;
            }
            $this->attributes['updated_at'] = $now;
            $this->dirty['updated_at'] = $now;
        }
        return $this->exists ? $this->update() : $this->insert();
    }

    public function update(): bool
    {
        if (empty($this->dirty)) return true;

        $sets = implode(', ', array_map(fn($k) => "`$k` = ?", array_keys($this->dirty)));
        $sql = "UPDATE " . static::$table . " SET $sets WHERE " . static::$primaryKey . " = ?";
        $params = [...array_values($this->dirty), $this->attributes[static::$primaryKey]];
        
        $result = static::db()->prepare($sql)->execute($params);
        if ($result) $this->dirty = [];
        return $result;
    }

    public function insert(): bool {
        $data = $this->attributes;
        $columns = implode("`, `", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $sql = "INSERT INTO " . static::$table . " (`$columns`) VALUES ($placeholders)";
        $stmt = static::db()->prepare($sql);
        $result = $stmt->execute(array_values($data));
        if ($result) {
            $this->attributes[static::$primaryKey] = static::db()->lastInsertedId();
            $this->exists = true;
            $this->dirty = [];
        }
        return $result;
    }

    public static function create(array $attributes): static {
        $instance = new static($attributes);
        $instance->save();
        return $instance;
    }
}
