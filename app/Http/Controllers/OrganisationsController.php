<?php
namespace App\Http\Controllers;

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Description of OrganisationsController
 *
 * @author G Brabyn
 */
class OrganisationsController extends Controller  
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        return view('organisations.index', [
            'orgs' => Organisation::where([])->orderBy('name')->get(),
        ]);
    }
    
    public function store(Request $request)
    {
        $validatedData = $request->validate($this->validators(null));
        Organisation::create($validatedData)->save();

        return redirect()->route('organisations');
    }
    
    private function validators(?Organisation $organisation=null) : array
    {
        $unique = $organisation 
                ? Rule::unique('organisation')->ignore($organisation) 
                : 'unique:organisation';
        
        return [
            'name' => ['required', 'max:255', $unique],
        ];
    }
    
    public function edit(Organisation $organisation)
    {
        $this->authorize('update', $organisation);
        
        return view('organisations.edit', compact('organisation'));
    }
    
    public function update(Request $request, Organisation $organisation)
    {
        $this->authorize('update', $organisation);
        
        $validatedData = $request->validate($this->validators($organisation));

        $organisation->name = $validatedData['name'];
        $organisation->save();
        
        return redirect()->route('organisations');
    }
    
    public function delete(Request $request)
    {
        $org = Organisation::where(['id'=>request('id')])->firstOrFail();

        \DB::transaction(function () use($org) {
            User::where('organisation_id', '=', $org->id)
                    ->where('type', '=', 'employee')
                    ->delete();

            $org->delete();
        });
        
        return redirect()->route('organisations');
    }
}
