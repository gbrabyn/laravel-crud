<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Organisation;
use App\Filters\UserFilters;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * CRUD for Users
 *
 * @author G Brabyn
 */
class UsersController extends Controller 
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $queryBuilder = UserFilters::apply($request);
        
        return view('users.index', [
            'users' => $queryBuilder->orderBy('name')->paginate(50),
            'types' => $this->getTypes(),
            'organisations' => $this->getOrganisations(),
        ]);
    }

    /**
     *  For populating <select> options
     */
    private function getOrganisations() : array
    {
        $ret = [];
        foreach(Organisation::where([])->orderBy('name', 'asc')->get() AS $organisation){
            $ret[$organisation->id] = $organisation->name;
        }
        
        return $ret;
    }
    
    /**
     *  For populating <select> and <input type="radio"> options
     */
    private function getTypes() : array
    {
        $types = array_combine(User::getTypes(), User::getTypes());
        $types[User::TYPE_EMPLOYEE] = 'Employee';
        $types[User::TYPE_ADMIN] = 'Administrator';
        
        return $types;
    }
    
    public function add()
    {
        $this->authorize('create', User::class);
        
        return view('users.edit', [
            'types' => $this->getTypes(),
            'organisations' => $this->getOrganisations(),
        ]);
    }
    
    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        
        $validatedData = $request->validate($this->getValidators());        
        $validatedData['password'] = $this->makePassword();
        
        User::create($validatedData)->save();

        return redirect()->route('users');
    }
    
    private function getValidators(?User $user=null) : array
    {
        $validOrganisations = ['', ...array_keys($this->getOrganisations())];
        $uniqueRule = $user ? Rule::unique('users')->ignore($user) : 'unique:users';
        
        return [
            'name' => 'required|max:255',
            'email' => ['required', 'email', 'max:255', $uniqueRule],
            'type' => ['required', Rule::in(User::getTypes())],
            'organisation_id' => [
                function($attribute, $value, $fail) {    // Validate required if type set to 'employee'
                    if(empty($value) && request()->type !== User::TYPE_ADMIN){
                        $fail('Required when "Type" is not "Administrator"');
                    }
                },
                Rule::in($validOrganisations),
            ],
        ];
    }
    
    private function makePassword() : string  
    {
        $random = str_shuffle('abcdefghjklmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ234567890!$%^&!$%^&');
        $password = substr($random, 0, 10);
        
        return Hash::make($password);
    }
    
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        
        return view('users.edit', [
            'types' => $this->getTypes(),
            'organisations' => $this->getOrganisations(),
            'user' => $user,
        ]);
    }
    
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
        
        $validatedData = $request->validate($this->getValidators($user));
        $user->update($validatedData);

        return redirect()->route('users');
    }
    
    public function delete(User $user)
    {
        $this->authorize('delete', $user);

        return [
            'userId' => $user->id,
            'success' => $user->delete(),
        ];
    }
}
