<?php namespace Bruha\Builder;
/**
 * Translate building class
 * @author Radek Brůha
 * @version 1.0
 */
class Translate extends BaseBuilder {
	/**
	 * Build and save application translate
	 * @param array $tables
	 */
	public function build(array $tables) {
		$translateListTemplate = [];
		foreach ($tables as $t) {
			$translate = [];
			foreach ($t->columns as $c) $translate[$c->name] = $c->comment ?: $c->name;
			$translateListTemplate[lcfirst($t->sanitizedName)] = ['list' => ['table' => ['head' => $translate], 'title' => $t->comment ?: $t->name],
				'change' => ['title' => '{0} Add new ' . lcfirst($t->comment ?: $t->name) . ' |[1,+Inf] Edit ' . lcfirst($t->comment ?: $t->name)]];
			$translatePresenterComponentChange[lcfirst($t->sanitizedName)] = ['component' => ['change' => $translate]];
		}

		$translate = ['presenter' => $translatePresenterComponentChange,
			'template' => $translateListTemplate,
			'common' => [
				'component' => [
					'presenter' => [
						'exist' => "We're sorry, but there are no items to show!",
						'found' => "We're sorry, but there are no items to show! Try remove your search options!",
						'change' => [
							'success' => 'Congratulation, item was succesfully saved.',
							'unique' => "We're sorry, but item couldn't be saved because of duplication data %1% in unique key %0%.",
							'other' => "We're sorry, but item couldn't be saved because of an error: %0%"
						],
						'delete' => [
							'success' => 'Congratulation, item was succesfully deleted.',
							'foreign' => "We're sorry, but item couldn't be deleted because of foreign key %0% defined on table %1% column %2%.",
							'other' => "We're sorry, but item couldn't be deleted because of an error: %0%"
						],
					],
					'page' => [
						'first' => 'Fisrt page',
						'last' => 'Last page'
					],
					'control' => [
						'add' => 'Add new item',
						'change' => 'Change item',
						'delete' => 'Delete item',
						'choose' => 'Choose item',
						'confirm' => 'Do you really want to delete this item?',
						'table' => 'Choose item from table',
						'save' => 'Save and item',
						'control' => 'Change filter and order',
						'submit' => 'Apply filtres and sorting',
						'where' => [
							'col' => 'Choose column',
							'mod' => 'Choose condition type',
							'val' => 'Add condition data',
							'add' => 'Add new filter line',
							'remove' => 'X',
							'operator' => [
								'less' => 'is less than',
								'lessEqual' => 'is less than or equal to',
								'equal' => 'is equal to',
								'notEqual' => 'is not equal to',
								'greaterEqual' => 'is greater than or equal to',
								'greater' => 'is greater than',
								'like' => 'contains',
								'notLike' => 'not contains',
								'null' => 'is not filled',
								'notNull' => 'is filled'
							]
						],
						'order' => [
							'col' => 'Choose column',
							'mod' => 'Choose condition type',
							'add' => 'Add new order line',
							'remove' => 'X',
							'operator' => [
								'desc' => 'from the greatest to the least',
								'asc' => 'from the least to the greatest'
							]
						]
					],
					'validator' => [
						'fill' => 'Sorry, this field must be filled!',
						'integer' => 'Sorry, this field must be an integer!',
						'range' => 'Sorry, this field must be between %0% and %1%!',
						'float' => 'Sorry, this field must be a float!',
						'length' => 'Sorry, this field must be equal or shorter than %0% characters.'
					]
				]
			]
		];
		\Bruha\Utils\File::write(__DIR__ . "/$this->projectPath/lang/generator.en_US.neon", preg_replace('~(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+~', "\n", (new \Nette\Neon\Encoder)->encode($translate, \Nette\Neon\Encoder::BLOCK)));
	}
}