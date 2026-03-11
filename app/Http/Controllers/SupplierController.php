<?php
// app/Http/Controllers/SupplierController.php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::latest()->paginate(10);
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact' => ['required', 'string', 'size:11', 'regex:/^09[0-9]{9}$/'],
            'email' => 'required|email|unique:suppliers,email',
            'address' => 'required|string'
        ], [
            'contact.size' => 'Contact number must be exactly 11 digits.',
            'contact.regex' => 'Contact number must start with 09 and contain only numbers.'
        ]);

        Supplier::create([
            'name' => $request->name,
            'contact' => $request->contact,
            'email' => $request->email,
            'address' => $request->address,
            'is_active' => 1 // Default to active for new suppliers
        ]);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    public function getSupplier($id)
    {
        $supplier = Supplier::findOrFail($id);
        return response()->json($supplier);
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact' => ['required', 'string', 'size:11', 'regex:/^09[0-9]{9}$/'],
            'email' => 'required|email|unique:suppliers,email,' . $supplier->id,
            'address' => 'required|string',
            'is_active' => 'sometimes|boolean'
        ], [
            'contact.size' => 'Contact number must be exactly 11 digits.',
            'contact.regex' => 'Contact number must start with 09 and contain only numbers.'
        ]);

        $supplier->update([
            'name' => $request->name,
            'contact' => $request->contact,
            'email' => $request->email,
            'address' => $request->address,
            'is_active' => $request->has('is_active') ? 1 : 0
        ]);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}