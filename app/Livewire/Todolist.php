<?php

namespace App\Livewire;

use App\Models\Todo;
use Exception;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Todolist extends Component
{

    use WithPagination;

    #[Rule('required|min:3|max:100')]
    public $name;
    public $search;
    public $editTodoID;
    #[Rule('required|min:3|max:100')]
    public $editTodoName;

    public function createToDo()
    {
        try {
            // validate input
            $validated = $this->validateOnly('name');
            // create todo
            Todo::create($validated);
            // clear the input
            $this->reset('name');
            // send flash message
            session()->flash('success', 'Todo created successfully');
            $this->resetPage();
        } catch (Exception $e) {
            session()->flash('error', 'todo create failed');
        }
    }

    public function editTodo($todoId)
    {
        $this->editTodoID = $todoId;
        $this->editTodoName = Todo::find($todoId)->name;
    }

    public function updateTodo()
    {
        try {
            $this->validateOnly('editTodoName');
            Todo::find($this->editTodoID)->update([
                'name' => $this->editTodoName,
            ]);
            session()->flash('success', 'todo updated successfully');
            $this->cancle();
        } catch (Exception $e) {
            session()->flash('error', 'Todo update failed');
            return;
        }
    }

    public function cancle()
    {
        $this->reset('editTodoID', 'editTodoName');
    }

    public function toggle($todoId)
    {
        try {
            $todo = Todo::find($todoId);
            $todo->completed = !$todo->completed;
            $todo->save();
        } catch (Exception $e) {
            session()->flash('error', 'Todo');
            return;
        }
    }

    public function delete($todoId)
    {
        try {
            Todo::findOrFail($todoId)->delete();
            session()->flash('success', 'Todo deleted successfully');
        } catch (Exception $e) {
            session()->flash('error', 'Todo not deleted');
            return;
        }
    }
    public function render()
    {
        $todos = Todo::latest()->where('name', 'like', "%{$this->search}%")->paginate(3);
        return view('livewire.todolist', [
            'todos' => $todos,
        ]);
    }
}
