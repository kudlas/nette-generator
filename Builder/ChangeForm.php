<?php namespace Bruha\Builder;
/**
 * Change form building class
 * @author Radek Brůha
 * @version 1.0
 */
class ChangeForm extends Base {
	/**
	 * Build and save add & edit form
	 * @param \Bruha\Utils\Object\Table $table
	 * @param array of \Utils\Object\Column $columns
	 * @param \stdClass $settings
	 * @return string Builded form
	 */
	public function build(\Bruha\Utils\Object\Table $table, \stdClass $settings) {
		$this->sourcePath = "/../Templates/$settings->templateName/form/change.latte";
		foreach ($table->columns as $column) {
			if ($column->key instanceof \Bruha\Utils\Object\Key\Primary) {
				$this->params['primaryKey'] = $column->name;
				if (strpos($column->extra, 'auto_increment') !== FALSE) continue;
			}
			$this->params['inputs'][] = $this->generateInputTypes($table, $column) .
				$this->generateHTML5Validations($column) .
				$this->generatePlaceholders($table, $column) .
				$this->generateRequiredValidations($column) .
				$this->generateRangeValidations($column) .
				$this->generateMaxLengthValidations($column) . ';';
		}
		return $this->buildTemplate();
	}

	/**
	 * @param \Utils\Object\Column $column Column
	 * @return string Input type
	 */
	private function generateInputTypes(\Bruha\Utils\Object\Table $table, \Bruha\Utils\Object\Column $column) {
		$name = 'generator.presenter.' . lcfirst($table->sanitizedName) . ".component.change.$column->name";
		if (in_array($column->type->name, ['boolean', 'enum', 'set'])) {
			return "->addSelect('$column->name', '$name', {$this->generateEnumSetValues($column)})";
		} else if ($column->key instanceof \Bruha\Utils\Object\Key\Foreign) {
			$table = implode('', array_map(function($value) {
					return ucfirst($value);
				}, explode('_', $column->key->table)));
			return "->addText('$column->name', '$name')->setAttribute('readonly', 'readonly')->setAttribute('data-table-target', '$table:list')";
		} else if (in_array($column->type->name, ['text', 'mediumtext', 'longtext', 'blob', 'mediumblob', 'longblob'])) {
			return "->addTextArea('$column->name', '$name')";
		} else return "->addText('$column->name', '$name')";
	}

	/**
	 * @param \Utils\Object\Column $column Column
	 * @return string Input values for BOOLean & ENUM & SET column types
	 */
	private function generateEnumSetValues(\Bruha\Utils\Object\Column $column) {
		if ($column->type->name === 'boolean') {
			return $column->nullable ? "['NULL' => '', 0 => 'FALSE', 1 => 'TRUE']" : "[0 => 'FALSE', 1 => 'TRUE']";
		} else {
			$values = '[';
			if ($column->nullable) $values .= "'NULL' => '', ";
			foreach ($column->type->extra as $value) $values .= "'$value' => '$value', ";
			$values .= ']';
			return str_replace(', ]', ']', $values);
		}
	}

	/**
	 * @param \Utils\Object\Column $column Column
	 * @return string Input HTML5 type
	 */
	private function generateHTML5Validations(\Bruha\Utils\Object\Column $column) {
		if (!(in_array($column->type->name, ['boolean', 'enum', 'set'], TRUE) || ($column->key instanceof \Bruha\Utils\Object\Key\Foreign))) {
			if (in_array($column->type->name, ['tinyint', 'smallint', 'mediumint', 'int', 'bigint', 'year'])) {
				return "->setType('number')";
			} elseif ($column->type->name === 'date') {
				return "->setType('date')";
			} elseif ($column->type->name === 'time') {
				return "->setType('time')";
			} elseif (in_array($column->type->name, ['datetime', 'timestamp'], TRUE)) {
				return "->setType('datetime')";
			}
		}
	}

	/**
	 * @param \Bruha\Utils\Object\Table $table
	 * @param \Utils\Object\Column $column Column
	 * @return string Input placeholder
	 */
	private function generatePlaceholders(\Bruha\Utils\Object\Table $table, \Bruha\Utils\Object\Column $column) {
		$name = 'generator.presenter.' . lcfirst($table->sanitizedName) . ".component.change.$column->name";
		return "->setAttribute('placeholder', '$name')";
	}

	/**
	 * @param \Utils\Object\Column $column Column
	 * @return string Input required validation
	 */
	private function generateRequiredValidations(\Bruha\Utils\Object\Column $column) {
		if (!$column->nullable && ($column->extra !== 'on update CURRENT_TIMESTAMP' && ($column->key instanceof \Bruha\Utils\Object\Key\Primary || $column->extra !== 'auto_increment'))) return "->addRule(Form::FILLED, 'generator.common.component.validator.fill')";
	}

	/**
	 * @param \Utils\Object\Column $column Column
	 * @return string Input range validation
	 */
	private function generateRangeValidations(\Bruha\Utils\Object\Column $column) {
		$range = NULL;
		if ($column->nullable || $column->extra === 'on update CURRENT_TIMESTAMP' || ($column->key instanceof \Bruha\Utils\Object\Key\Primary && $column->extra === 'auto_increment')) $range = '->addCondition(Form::FILLED)';
		//	if (!$column->key instanceof \Bruha\Utils\Object\Key\Foreign) {
		if (in_array($column->type->name, ['tinyint', 'smallint', 'mediumint', 'int', 'bigint'])) {
			$range .= "->addRule(Form::INTEGER, 'generator.common.component.validator.integer')";
			switch ($column->type->name) {
				case 'tinyint': return ($range .= $column->type->extra ? "->addRule(Form::RANGE, \$this->translator->translate('generator.common.component.validator.range', NULL, [0, 255]), [0, 255])" : "->addRule(Form::RANGE, \$this->translator->translate('generator.common.component.validator.range', NULL, [-128, 127]), [-128, 127])");
				case 'smallint': return ($range .= $column->type->extra ? "->addRule(Form::RANGE, \$this->translator->translate('generator.common.component.validator.range', NULL, [0, 65535]), [0, 65535])" : "->addRule(Form::RANGE, \$this->translator->translate('generator.common.component.validator.range', NULL, [-32768, 32767]), [-32768, 32767])");
				case 'mediumint': return ($range .= $column->type->extra ? "->addRule(Form::RANGE, \$this->translator->translate('generator.common.component.validator.range', NULL, [0, 16777215]), [0, 16777215])" : "->addRule(Form::RANGE, \$this->translator->translate('generator.common.component.validator.range', NULL, [-8388608, 8388607]), [-8388608, 8388607])");
				case 'int': return ($range .= $column->type->extra ? "->addRule(Form::RANGE, \$this->translator->translate('generator.common.component.validator.range', NULL, [0, 4294967295]), [0, 4294967295])" : "->addRule(Form::RANGE, \$this->translator->translate('generator.common.component.validator.range', NULL, [-2147483648, 2147483647]), [-2147483648, 2147483647])");
				case 'bigint': return ($range .= $column->type->extra ? "->addRule(Form::RANGE, \$this->translator->translate('generator.common.component.validator.range', NULL, [0, 18446744073709551615]), [0, 18446744073709551615])" : "->addRule(Form::RANGE, \$this->translator->translate('generator.common.component.validator.range', NULL, [-9223372036854775808, 9223372036854775807]), [-9223372036854775808, 9223372036854775807])");
				case 'year': return ($range .= (int)$column->type->length === 4 ? "->addRule(Form::RANGE, \$this->translator->translate('generator.common.component.validator.range', NULL, [1901, 2155]), [1901, 2155])" : "->addRule(Form::RANGE, \$this->translator->translate('generator.common.component.validator.range', NULL, [0, 99]), [0, 99])");
			}
		} else if (in_array($column->type->name, ['float', 'double', 'decimal'])) return ($range .= "->addRule(Form::FLOAT, 'generator.common.component.validator.float')");
		//	}
		return $range;
	}

	/**
	 * @param \Utils\Object\Column $column Column
	 * @return string Input max legth validation
	 */
	private function generateMaxLengthValidations(\Bruha\Utils\Object\Column $column) {
		switch ($column->type->name) {
			case 'char': return "->addRule(Form::MAX_LENGTH, \$this->translator->translate('generator.common.component.validator.length', NULL, [{$column->type->length}]), {$column->type->length})";
			case 'varchar': return "->addRule(Form::MAX_LENGTH, \$this->translator->translate('generator.common.component.validator.length', NULL, [{$column->type->length}]), {$column->type->length})";
			case 'tinytext': return "->addRule(Form::MAX_LENGTH, \$this->translator->translate('generator.common.component.validator.length', NULL, [255]), 255)";
			case 'text': return "->addRule(Form::MAX_LENGTH, \$this->translator->translate('generator.common.component.validator.length', NULL, [65535]), 65535)";
			case 'mediumtext': return "->addRule(Form::MAX_LENGTH, \$this->translator->translate('generator.common.component.validator.length', NULL, [16777215]), 16777215)";
			case 'longtext': return "->addRule(Form::MAX_LENGTH, \$this->translator->translate('generator.common.component.validator.length', NULL, [4294967295]), 4294967295)";
			case 'binary': return "->addRule(Form::MAX_LENGTH, \$this->translator->translate('generator.common.component.validator.length', NULL, [{$column->type->length}]), {$column->type->length})";
			case 'varbinary': return "->addRule(Form::MAX_LENGTH, \$this->translator->translate('generator.common.component.validator.length', NULL, [{$column->type->length}]), {$column->type->length})";
			case 'tinyblob': return "->addRule(Form::MAX_LENGTH, \$this->translator->translate('generator.common.component.validator.length', NULL, [255]), 255)";
			case 'blob': return "->addRule(Form::MAX_LENGTH, \$this->translator->translate('generator.common.component.validator.length', NULL, [65535]), 65535)";
			case 'mediumblob': return "->addRule(Form::MAX_LENGTH, \$this->translator->translate('generator.common.component.validator.length', NULL, [16777215]), 16777215)";
			case 'longblob': return "->addRule(Form::MAX_LENGTH, \$this->translator->translate('generator.common.component.validator.length', NULL, [4294967295]), 4294967295)";
		}
	}
}