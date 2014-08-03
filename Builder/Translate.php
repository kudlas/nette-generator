<?php namespace Bruha\Builder;
/**
 * Translate building class
 * @author Radek Brůha
 * @version 1.0
 */
class Translate extends Base {
	/**
	 * Build and save application translate
	 * @param array $tables
	 */
	public function build(array $tables) {
		$translateListTemplate = [];
		foreach ($tables as $t) {
			$translate = [];
			foreach ($t->columns as $c) $translate[$c->name] = $c->comment ?: $c->name;
			$translateListTemplate[lcfirst($t->sanitizedName)] = ['list' => ['table' => ['head' => $translate], 'title' => $t->comment ?: $t->name]];
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
							'unique' => "We're sorry, but item couldn't be saved because of duplicity data %1% in unique key %0%.",
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
						'control' => 'Change filter and order'
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