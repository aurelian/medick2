<?php
// $Id: SQLType.php 458 2007-08-26 18:58:03Z aurelian $
// This file is part of ActiveRecord5, a Medick (http://medick.locknet.ro) Experiment

class SQLType extends Object {

  // sql type to php type
  public static function getPhpType( $type ) {
    if( $type == 'integer' || $type == 'int') return 'Integer';
    else return 'String';
    // elseif( $type == 'varchar' || $type == 'string' || $type == 'text') return 'String';
    // elseif( $type == 'timestamp' || $type == 'time' || $type == 'date') return 'Time';
    // else throw new SQLException('Unknow type: "' . $type . '"');
  }

}

