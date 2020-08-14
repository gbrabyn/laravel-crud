<?php
namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Applies request filters to database queries
 *
 * @author G Brabyn
 */
interface FiltersInterface 
{
    public static function apply(Request $filters) : Builder;
}
