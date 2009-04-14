<?php
// $Id: SQLCommand.php 461 2007-08-31 09:47:09Z aurelian $
// This file is part of ActiveRecord5, a Medick (http://medick.locknet.ro) Experiment

/**
 * It represents an sql command
 *
 * You can use this object to build sql query`s in a fancy way:
 * <code>
 *  $command= SQLCommand::select()->from('news')->where('state=?')->orderBy('created_at');
 * // later, you can use a PreparedStatement to bind parameters.
 *  $stmt= $conn->prepareStatement($command->toSQL());
 *  $stmt->setInt(1, News::PUBLISHED);
 *  $rs= $stmt->executeQuery();
 * </code>
 * More methods will be added later-on, API will be provided on request.
 * 
 * @package medick.active.record
 * @author Aurelian Oancea
 * @since Rev. 343
 */
class SQLCommand extends Object {

    private $command;

    private $tables= array();

    private $joins= array();
    
    private $wheres= array();

    private $orderBy;

    private $columns;
    
    private function SQLCommand($command) {
        $this->command= $command;
    }

    public static function select() {
        return new SQLCommand('select');
    }

    public function from($table) {
        $this->tables[]= $table;
        return $this;
    }

    public function where($clause) {
        $this->wheres[]= $clause;
        return $this;
    }

    public function orderBy($clause) {
        $this->orderBy= $clause;
        return $this;
    }

    public function columns($columns) {
        $this->columns= $columns;
        return $this;
    }
    
    public function leftJoin($what) {
        // $this->tables[]=$what;
        $this->joins[]= $what;
        return $this;
    }
    
    public function toSQL() {
        $query= $this->command . " ";
        // if ($this->distinct) $query .= "distinct ";
        $query .= $this->appendColumns();
        // $query .= " from " . $this->from;
        $query .= $this->appendFrom();
        $query .= $this->appendJoins();
        $query .= $this->appendWhere();
        $query .= $this->appendOrderBy();
        return $query;
    }

    private function appendColumns() {
        return $this->columns ? $this->columns : "*";
    }

    private function appendFrom() {
        $q= " from ";
        $size= count($this->tables);
        for ($i = 0; $i < $size; ++$i) {
            $q .= $this->tables[$i];
            if ($i <= $size - 2) {
                $q .= " , ";
            }
        }
        return $q;
    }
    
    private function appendJoins() {
        if (count($this->joins)) return " " . implode(" ", $this->joins);
        else return " ";
    }
    
    private function appendWhere() {
        if (count($this->wheres)) return " where " . implode(" and ", $this->wheres);
        else return "";
    }

    private function appendOrderBy() {
        return $this->orderBy ? " order by " . $this->orderBy : "";
    }

}

