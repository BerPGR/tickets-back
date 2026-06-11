<?php

abstract class Model
{
    protected static string $table = "";
    protected static string $primaryKey = "";
    protected static array $hidden = ['password_hash'];
    protected static array $attributes = [];
    protected static array $dirty = [];

    protected static bool $timestamps = false;
    protected static array $instanceWheres = [];
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
        return $this->attributes;
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
    }
}
