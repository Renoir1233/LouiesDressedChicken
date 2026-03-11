<?php
// app/Http/Controllers/EmployeeController.php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $employees = Employee::when($search, function($query) use ($search) {
            return $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
        })->latest()->get();

        return view('employees.index', compact('employees', 'search'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'position' => 'required|string|in:Admin,Manager,Cashier,Driver,Helper',
            'address' => 'required|string|max:500',
            'contact_no' => ['required', 'string', 'size:11', 'regex:/^09[0-9]{9}$/'],
            'status' => 'required|in:Active,Inactive'
        ], [
            'contact_no.size' => 'Contact number must be exactly 11 digits.',
            'contact_no.regex' => 'Contact number must start with 09 and contain only numbers.',
            'position.in' => 'Please select a valid position.'
        ]);

        // Check if email already exists
        $existingEmployee = Employee::where('email', $request->email)->first();
        if ($existingEmployee) {
            return redirect()->back()->withErrors(['email' => 'Email already exists.'])->withInput();
        }

        Employee::create($request->all());

        return redirect()->route('employees.index')
                        ->with('success', 'Employee Successfully Registered!!!');
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'position' => 'required|string|in:Admin,Manager,Cashier,Driver,Helper',
            'address' => 'required|string|max:500',
            'contact_no' => ['required', 'string', 'size:11', 'regex:/^09[0-9]{9}$/'],
            'status' => 'required|in:Active,Inactive'
        ], [
            'contact_no.size' => 'Contact number must be exactly 11 digits.',
            'contact_no.regex' => 'Contact number must start with 09 and contain only numbers.',
            'position.in' => 'Please select a valid position.'
        ]);

        // Check if email already exists for other employees
        $existingEmployee = Employee::where('email', $request->email)
                                  ->where('id', '!=', $employee->id)
                                  ->first();
        if ($existingEmployee) {
            return redirect()->back()->withErrors(['email' => 'Email already exists.'])->withInput();
        }

        $employee->update($request->all());

        return redirect()->route('employees.index')
                        ->with('success', 'Employee updated successfully!');
    }

    public function getEmployee($id)
    {
        $employee = Employee::findOrFail($id);
        return response()->json($employee);
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('employees.index')
                        ->with('success', 'Employee deleted successfully!');
    }
}