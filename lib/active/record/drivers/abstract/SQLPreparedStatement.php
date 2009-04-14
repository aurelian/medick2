<?php
// $Id: SQLPreparedStatement.php 461 2007-08-31 09:47:09Z aurelian $
// This file is part of ActiveRecord5, a Medick (http://medick.locknet.ro) Experiment

abstract class SQLPreparedStatement extends Object {

  protected $conn, $sql;

  protected $limit= -1;

  protected $offset= -1;

  protected $positions=0;
  protected $positionsCount=0;
  protected $sql_cache='';
  protected $sql_cache_valid= false;
  protected $boundInVars= array();

  public function SQLPreparedStatement(SQLConnection $conn, $sql) {
    $this->conn= $conn;
    $this->sql= $sql;
    $this->positions = $this->parseQuery ( $sql );
    // save processing later in cases where we may repeatedly exec statement
    $this->positionsCount = count ( $this->positions );
  }

  /**
   * Parse the SQL query for ? positions
   *
   * @param string $sql The query to process
   * @return array Positions from the start of the string that ?'s appear at
   */
  protected function parseQuery ( $sql ) {
    $positions = array();
    // match anything ? ' " or \ in $sql with an early out if we find nothing
    if ( preg_match_all ( '([\?]|[\']|[\"]|[\\\])', $sql, $matches, PREG_OFFSET_CAPTURE ) !== 0 ) {
      $matches = $matches['0'];
      $open = NULL;
      // go thru all our matches and see what we can find
      for ( $i = 0, $j = count ( $matches ); $i < $j; $i++ ) {
        switch ( $matches[$i]['0'] ) {
          // if we already have an open " or ' then check if this is the end to close it or not
          case $open:
            $open = NULL;
            break;
          // we have a quote, set ourselves open
          case '"':
          case "'":
            $open = $matches[$i]['0'];
            break;
          // check if it is an escaped quote and skip if it is
          case '\\':
            $next_match = $matches[$i+1]['0'];
            if ( $next_match === '"' || $next_match === "'" ) {
              $i++;
            }
            unset ( $next_match );
            break;
				  // we found a ?, check we arent in an open "/' first and
				  // add it to the position list if we arent
          default:
            if ( $open === NULL ) {
              $positions[] = $matches[$i]['1'];
            }
        } // switch
        unset ( $matches[$i] );
      } // for
      unset ( $open, $matches, $i, $j );
    } // if
	  return $positions;
  }

  /**
   * Replaces placeholders with the specified parameter values in the SQL.
   * 
   * This is for emulated prepared statements.
   * 
   * @return string New SQL statement with parameters replaced.
   * @throws SQLException - if param not bound.
   */
  protected function replaceParams(Array $params= array()) {
    $this->setupParams($params);
    // early out if we still have the same query ready
    if ( $this->sql_cache_valid === true ) return $this->sql_cache;
    // Default behavior for this function is to behave in 'emulated' mode.    
    $sql = '';    
    $last_position = 0;

    for ($position = 0; $position < $this->positionsCount; $position++) {
      if (!isset($this->boundInVars[$position + 1])) {
        throw new SQLException('Replace params: undefined query param: ' . ($position + 1));
      }
      $current_position = $this->positions[$position];            
      $sql .= substr($this->sql, $last_position, $current_position - $last_position);
      $sql .= $this->boundInVars[$position + 1];                    
      $last_position = $current_position + 1;            
    }
    // append the rest of the query
    $sql .= substr($this->sql, $last_position);
    // just so we dont touch anything with a blob/clob
    if ( strlen ( $sql ) > 2048 ) { 
		  $this->sql_cache = $sql;
      $this->sql_cache_valid = true;
		  return $this->sql_cache;
	  } else {
		  return $sql;
	  }
  }

  public function setString($idx, $value) {
    $this->boundInVars[$idx] = "'" . $this->escape((string)$value) . "'";
  }

  public function setInteger($idx, $value) {
    $this->boundInVars[$idx]= (int)$value;
  }

  // todo
  public function set( $idx, $value ) {
    $t= gettype($value);
    switch($t) {
      case "integer":
        return $this->setInteger($idx, $value);
      default:
        throw new MedickException(__METHOD__ . " not implemented for type: ".$t." !");
    }
  }

  public function populateValues(Array $fields) {
    $i=1; foreach($fields as $field) {
      if(!$field instanceof SQLField) throw new SQLException('Wrong argument Type, it should be an instance of "Field"');
      call_user_func( array($this, 'set'.ucfirst($field->getType())), $i++, $field->getValue() );
    }
  }

  public function setLimit($limit) {
    $this->limit= $limit;
  }

  public function setOffset($offset) {
    $this->offset= $offset;
  }

  public function executeQuery(Array $params= array()) {
    $sql= $this->replaceParams($params);
    if ($this->limit > 0 || $this->offset > 0) {
      $this->conn->applyLimit($sql, $this->limit, $this->offset);
    }
    return $this->conn->execute( $sql );
  }

  public function getAllRecords(Array $params=array(), ReflectionClass $record) {
    $result= $this->conn->exec( $this->replaceParams($params) );
    return $this->getRecordsIterator( $result, $record );

  }

  public function executeUpdate() {
    return $this->conn->executeUpdate( $this->replaceParams() );
  }

  public function close() {  
  }

  private function setupParams(Array $params=array()) {
    if ($params) {
			for($i=0,$cnt=count($params); $i < $cnt; $i++) {
				$this->set($i+1, $params[$i]);
			}
    }
  }

  abstract public function escape( $value );

  abstract protected function getRecordsIterator( $results, ReflectionClass $class );

}

