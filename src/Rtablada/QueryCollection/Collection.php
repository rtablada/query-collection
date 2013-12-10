<?php namespace Rtablada\QueryCollection;

use Illuminate\Support\ServiceProvider;

class Collection extends \Illuminate\Database\Eloquent\Collection
{
	/**
	 * All of the available clause operators.
	 *
	 * @var array
	 */
	protected $operators = array(
		'=', '<', '>', '<=', '>=', '<>', '!=',
		'like', 'not like', 'between', 'ilike',
		'&', '|', '^', '<<', '>>',
	);

	public function where($column, $operator = null, $value = null)
	{
		if (func_num_args() == 2) {
			list($value, $operator) = array($operator, '=');
		} elseif ($this->invalidOperatorAndValue($operator, $value)) {
			throw new \InvalidArgumentException("Value must be provided.");
		}

		return $this->filter(function($model) use ($column, $operator, $value) {
			return $this->whereQuery($model, $column, $operator, $value);
		});
	}

	public function firstWhere($column, $operator = null, $value = null)
	{
		if (func_num_args() == 2) {
			list($value, $operator) = array($operator, '=');
		} elseif ($this->invalidOperatorAndValue($operator, $value)) {
			throw new \InvalidArgumentException("Value must be provided.");
		}

		$results = $this->where($column, $operator, $value);

		return isset($results[0]) ? $results[0] : null;
	}

	protected function whereQuery($model, $column, $operator, $value)
	{
		switch ($operator) {
				case '=':
					return $model->{$column} == $value;
					break;

				case '<>':
					return $model->{$column} != $value;
					break;

				case '<':
					return $model->{$column} < $value;
					break;

				case '>':
					return $model->{$column} > $value;
					break;

				case '<=':
					return $model->{$column} <= $value;
					break;

				case '>=':
					return $model->{$column} >= $value;
					break;

				case 'BETWEEN':
					return $model->{$column} > $value[0] && $model->{$column} < $value[1];
					break;
			}
	}

	/**
	 * Determine if the given operator and value combination is legal.
	 *
	 * @param  string  $operator
	 * @param  mxied  $value
	 * @return bool
	 */
	protected function invalidOperatorAndValue($operator, $value)
	{
		$isOperator = in_array($operator, $this->operators);

		return ($isOperator && $operator != '=' && is_null($value));
	}

}
