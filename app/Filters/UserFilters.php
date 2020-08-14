<?php
namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\User;

/**
 * Applies request filters to Users table
 *
 * @author G Brabyn
 */
class UserFilters implements FiltersInterface
{
    protected static $whiteListFilters = [
        'organisation', 'type', 'nameOrEmail'
    ];
    
    public static function apply(Request $filters) : Builder
    {
        $queryBuilder = (new User)->newQuery();
        
        foreach ($filters->all() as $filterName => $value) {
            if(\in_array($filterName, self::$whiteListFilters) && $value !== null){
                $queryBuilder = self::$filterName($queryBuilder, $value);
            }
        }
        
        return $queryBuilder;
    }
    
    private static function organisation(Builder $queryBuilder, $value) : Builder
    {
        return $queryBuilder->where('organisation_id', '=', $value);
    }
    
    private static function type(Builder $queryBuilder, $value) : Builder
    {
        return $queryBuilder->where('type', '=', $value);
    }
    
    private static function nameOrEmail(Builder $queryBuilder, $value) : Builder
    {
        return $queryBuilder->where(function($query) use($value) {
            $query  ->where('name', 'sounds like', $value)
                    ->orWhereRaw('UPPER(email) = ?', [strtoupper($value)]);
        });
    }
}
