<?php
/* SVN FILE: $Id$ */
/**
 * PostgreSQL DBO拡張
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.models.datasources.dbo
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
App::import('Core','DboPostgres');
class DboPostgresEx extends DboPostgres {
/**
 * Returns an array of the fields in given table name.
 *
 * @param string $tableName Name of database table to inspect
 * @return array Fields in table. Keys are name and type
 */
	function &describe(&$model) {
		$fields = parent::describe($model);
		$table = $this->fullTableName($model, false);
		$this->_sequenceMap[$table] = array();

		if ($fields === null) {
			$cols = $this->fetchAll(
				"SELECT DISTINCT column_name AS name, data_type AS type, is_nullable AS null,
					column_default AS default, ordinal_position AS position, character_maximum_length AS char_length,
					character_octet_length AS oct_length FROM information_schema.columns
				WHERE table_name = " . $this->value($table) . " AND table_schema = " .
				$this->value($this->config['schema'])."  ORDER BY position",
				false
			);

			foreach ($cols as $column) {
				$colKey = array_keys($column);

				if (isset($column[$colKey[0]]) && !isset($column[0])) {
					$column[0] = $column[$colKey[0]];
				}

				if (isset($column[0])) {
					$c = $column[0];

					if (!empty($c['char_length'])) {
						$length = intval($c['char_length']);
					} elseif (!empty($c['oct_length'])) {
						if ($c['type'] == 'character varying') {
							$length = null;
							$c['type'] = 'text';
						// >>> CUSTOMIZE ADD 2011/08/22 ryuring
						} elseif($c['type'] == 'text') {
							$length = null;
						// <<<
						} else {
							$length = intval($c['oct_length']);
						}
					} else {
						$length = $this->length($c['type']);
					}
					$fields[$c['name']] = array(
						'type'    => $this->column($c['type']),
						'null'    => ($c['null'] == 'NO' ? false : true),
						'default' => preg_replace(
							"/^'(.*)'$/",
							"$1",
							preg_replace('/::.*/', '', $c['default'])
						),
						'length'  => $length
					);
					// >>> CUSTOMIZE ADD 2011/08/22 ryuring
					if (!$fields[$c['name']]['length'] && $fields[$c['name']]['type'] == 'integer') {
						$fields[$c['name']]['length'] = 8;
					}
					// <<<
					if ($c['name'] == $model->primaryKey) {
						$fields[$c['name']]['key'] = 'primary';
						if ($fields[$c['name']]['type'] !== 'string') {
							// >>> CUSTOMIZE MODIFY 2011/08/22 ryuring
							//$fields[$c['name']]['length'] = 11;
							// ---
							$fields[$c['name']]['length'] = 8;
							// <<<
						}
					}
					if (
						$fields[$c['name']]['default'] == 'NULL' ||
						preg_match('/nextval\([\'"]?([\w.]+)/', $c['default'], $seq)
					) {
						$fields[$c['name']]['default'] = null;
						if (!empty($seq) && isset($seq[1])) {
							$this->_sequenceMap[$table][$c['name']] = $seq[1];
						}
					}
					// >>> CUSTOMIZE ADD 2011/08/22 ryuring
					if($fields[$c['name']]['default'] === 'true' && $fields[$c['name']]['type'] == 'boolean') {
						$fields[$c['name']]['default'] = 1;
					} elseif($fields[$c['name']]['default'] === 'false' && $fields[$c['name']]['type'] == 'boolean') {
						$fields[$c['name']]['default'] = 0;
					}
					// <<<
				}
			}
			$this->__cacheDescription($table, $fields);
		}
		if (isset($model->sequence)) {
			$this->_sequenceMap[$table][$model->primaryKey] = $model->sequence;
		}
		return $fields;
	}
/**
 * テーブル名のリネームステートメントを生成
 *
 * @param string $sourceName
 * @param string $targetName
 * @return string
 * @access public
 */
	function buildRenameTable($sourceName, $targetName) {
		
		return "ALTER TABLE ".$sourceName." RENAME TO ".$targetName;
		
	}
/**
 * カラム名を変更する
 *
 * @param array $options [ table / new / old  ]
 * @return boolean
 * @access public
 */
	function renameColumn($options) {

		extract($options);

		if(!isset($table) || !isset($new) || !isset($old)) {
			return false;
		}

		$table = $this->config['prefix'] . $table;
		$sql = 'ALTER TABLE "'.$table.'" RENAME "'.$old.'" TO "'.$new.'"';
		return $this->execute($sql);
		
	}
/**
 * Returns a quoted and escaped string of $data for use in an SQL statement.
 *
 * @param string $data String to be prepared for use in an SQL statement
 * @param string $column The column into which this data will be inserted
 * @param boolean $read Value to be used in READ or WRITE context
 * @return string Quoted and escaped
 * @todo Add logic that formats/escapes data based on column type
 * @access public
 */
	function value($data, $column = null, $read = true) {

		// >>> CUSTOMIZE MODIFY 2011/03/23 ryuring
		//$parent = parent::value($data, $column);
		// ---
		$parent = $this->__value($data, $column);
		// <<<
		if ($parent != null) {
			return $parent;
		}

		if ($data === null) {
			return 'NULL';
		}
		if (empty($column)) {
			$column = $this->introspectType($data);
		}

		switch($column) {
			case 'binary':
				$data = pg_escape_bytea($data);
			break;
			case 'boolean':
				if ($data === true || $data === 't' || $data === 'true') {
					return 'TRUE';
				} elseif ($data === false || $data === 'f' || $data === 'false') {
					return 'FALSE';
				}
				return (!empty($data) ? 'TRUE' : 'FALSE');
			break;
			case 'float':
				if (is_float($data)) {
					$data = sprintf('%F', $data);
				}
			case 'inet':
			case 'integer':
			case 'date':
			case 'datetime':
			case 'timestamp':
			case 'time':
				// >>> CUSTOMIZE ADD 2010/03/23 ryuring
				// postgresql の場合、0000-00-00 00:00:00 を指定すると範囲外エラーとなる為
				if ($data === '0000-00-00 00:00:00') {
					return "'".date('Y-m-d H:i:s', 0)."'";
				}
				// <<<
				if ($data === '') {
					return $read ? 'NULL' : 'DEFAULT';
				}
			default:
				$data = pg_escape_string($data);
			break;
		}
		return "'" . $data . "'";
		
	}
/**
 * Prepares a value, or an array of values for database queries by quoting and escaping them.
 *
 * @param mixed $data A value or an array of values to prepare.
 * @param string $column The column into which this data will be inserted
 * @param boolean $read Value to be used in READ or WRITE context
 * @return mixed Prepared value or array of values.
 * @access private
 */
	function __value($data, $column = null, $read = true) {
		
		if (is_array($data) && !empty($data)) {
			return array_map(
				array(&$this, 'value'),
				$data, array_fill(0, count($data), $column), array_fill(0, count($data), $read)
			);
		} elseif (is_object($data) && isset($data->type)) {
			if ($data->type == 'identifier') {
				return $this->name($data->value);
			} elseif ($data->type == 'expression') {
				return $data->value;
			}
		} elseif (in_array($data, array('{$__cakeID__$}', '{$__cakeForeignKey__$}'), true)) {
			return $data;
		} else {
			return null;
		}
		
	}
/**
 * Alter the Schema of a table.
 *
 * @param array $compare Results of CakeSchema::compare()
 * @param string $table name of the table
 * @access public
 * @return array
 */
	function alterSchema($compare, $table = null) {
		
		if (!is_array($compare)) {
			return false;
		}
		$out = '';
		$colList = array();
		foreach ($compare as $curTable => $types) {
			$indexes = array();
			if (!$table || $table == $curTable) {
				$out .= 'ALTER TABLE ' . $this->fullTableName($curTable) . " \n";
				foreach ($types as $type => $column) {
					if (isset($column['indexes'])) {
						$indexes[$type] = $column['indexes'];
						unset($column['indexes']);
					}
					switch ($type) {
						case 'add':
							foreach ($column as $field => $col) {
								$col['name'] = $field;
								$alter = 'ADD COLUMN '.$this->buildColumn($col);
								if (isset($col['after'])) {
									$alter .= ' AFTER '. $this->name($col['after']);
								}
								$colList[] = $alter;
							}
						break;
						case 'drop':
							foreach ($column as $field => $col) {
								$col['name'] = $field;
								$colList[] = 'DROP COLUMN '.$this->name($field);
							}
						break;
						case 'change':
							// CUSTOMIZE DEL 2010/05/16 ryuring
							//==================================================
							// PostgreSQLの場合、schemaでDB側の数値型の長さが取得できない為、
							// 変更されてない場合でも変更されてしまうので、chageは無視する
							// 仕様に変更（暫定措置）
							//==================================================
							/*foreach ($column as $field => $col) {
								if (!isset($col['name'])) {
									$col['name'] = $field;
								}
								$fieldName = $this->name($field);
								$colList[] = 'ALTER COLUMN '. $fieldName .' TYPE ' . str_replace($fieldName, '', $this->buildColumn($col));
							}*/
						break;
					}
				}
				if (isset($indexes['drop']['PRIMARY'])) {
					$colList[] = 'DROP CONSTRAINT ' . $curTable . '_pkey';
				}
				if (isset($indexes['add']['PRIMARY'])) {
					$cols = $indexes['add']['PRIMARY']['column'];
					if (is_array($cols)) {
						$cols = implode(', ', $cols);
					}
					$colList[] = 'ADD PRIMARY KEY (' . $cols . ')';
				}
				
				if (!empty($colList)) {
					$out .= "\t" . implode(",\n\t", $colList) . ";\n\n";
				} else {
					$out = '';
				}
				$out .= implode(";\n\t", $this->_alterIndexes($curTable, $indexes)) . ";";
			}
		}
		return $out;
		
	}
	
}
?>
