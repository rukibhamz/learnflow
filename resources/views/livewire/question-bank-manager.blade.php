<div>
    @if (session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800">{{ session('success') }}</div>
    @endif

    <div class="flex flex-wrap items-center gap-3 mb-6">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search questions..." class="flex-1 min-w-[200px] h-10 px-4 border border-rule rounded-card text-sm focus:outline-none focus:border-primary">
        <select wire:model.live="categoryFilter" class="h-10 px-4 border border-rule rounded-card text-sm bg-surface">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat }}">{{ $cat }}</option>
            @endforeach
        </select>
        <button wire:click="openForm" class="h-10 px-5 bg-ink text-white font-display font-bold text-sm rounded-card hover:opacity-90 transition-opacity">+ Add Question</button>
    </div>

    <div class="space-y-3">
        @forelse($questions as $bq)
            <div wire:key="bq-{{ $bq->id }}" class="bg-surface border border-rule rounded-card p-4 flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2 py-0.5 bg-primary/10 text-primary text-[10px] font-bold uppercase rounded">{{ str_replace('_', ' ', $bq->type->value) }}</span>
                        @if($bq->category)
                            <span class="px-2 py-0.5 bg-bg text-ink3 text-[10px] font-bold uppercase rounded">{{ $bq->category }}</span>
                        @endif
                        <span class="text-[10px] text-ink3">{{ $bq->points }} pt{{ $bq->points > 1 ? 's' : '' }}</span>
                    </div>
                    <p class="text-sm text-ink">{{ Str::limit($bq->question, 120) }}</p>
                </div>
                <div class="flex items-center gap-1 shrink-0">
                    <button wire:click="openForm({{ $bq->id }})" class="p-1.5 text-ink3 hover:text-primary transition-colors" title="Edit">
                        <span class="material-symbols-outlined text-[18px]">edit</span>
                    </button>
                    <button wire:click="delete({{ $bq->id }})" wire:confirm="Delete this question from the bank?" class="p-1.5 text-ink3 hover:text-red-600 transition-colors" title="Delete">
                        <span class="material-symbols-outlined text-[18px]">delete</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center py-12 text-ink3 text-sm">No questions in the bank yet. Add your first question above.</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $questions->links() }}</div>

    {{-- Form Modal --}}
    @if($showForm)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click.self="$wire.closeForm()">
            <div class="bg-surface rounded-xl w-full max-w-lg p-6 border border-rule max-h-[90vh] overflow-y-auto">
                <h3 class="font-display font-bold text-lg text-ink mb-4">{{ $editingId ? 'Edit' : 'Add' }} Bank Question</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Question *</label>
                        <textarea wire:model="question" rows="3" class="w-full px-4 py-2 border border-rule rounded-card text-sm resize-none focus:outline-none focus:border-primary"></textarea>
                        @error('question') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Type</label>
                            <select wire:model.live="type" class="w-full h-10 px-3 border border-rule rounded-card text-sm bg-surface">
                                <option value="mcq">Multiple Choice</option>
                                <option value="true_false">True / False</option>
                                <option value="short_answer">Short Answer</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Category</label>
                            <input type="text" wire:model="category" class="w-full h-10 px-3 border border-rule rounded-card text-sm" placeholder="e.g. HTML, CSS">
                        </div>
                    </div>

                    @if($type === 'mcq')
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Options</label>
                            <div class="space-y-2">
                                @foreach($options as $oi => $opt)
                                    <div class="flex items-center gap-2">
                                        <input type="radio" wire:model="correct_answer" value="{{ $oi }}" class="text-primary">
                                        <input type="text" wire:model="options.{{ $oi }}" class="flex-1 h-9 px-3 border border-rule rounded-card text-sm" placeholder="Option {{ $oi + 1 }}">
                                        @if(count($options) > 2)
                                            <button wire:click="removeOption({{ $oi }})" class="text-ink3 hover:text-red-600"><span class="material-symbols-outlined text-[16px]">close</span></button>
                                        @endif
                                    </div>
                                @endforeach
                                <button wire:click="addOption" class="text-primary text-xs font-medium hover:underline">+ Add option</button>
                            </div>
                        </div>
                    @elseif($type === 'true_false')
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2"><input type="radio" wire:model="correct_answer" value="true" class="text-primary"> <span class="text-sm">True</span></label>
                            <label class="flex items-center gap-2"><input type="radio" wire:model="correct_answer" value="false" class="text-primary"> <span class="text-sm">False</span></label>
                        </div>
                    @else
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Correct Answer *</label>
                            <input type="text" wire:model="correct_answer" class="w-full h-10 px-3 border border-rule rounded-card text-sm" placeholder="Expected answer">
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Points</label>
                            <input type="number" wire:model="points" min="1" class="w-full h-10 px-3 border border-rule rounded-card text-sm">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Explanation</label>
                            <input type="text" wire:model="explanation" class="w-full h-10 px-3 border border-rule rounded-card text-sm" placeholder="Optional">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="closeForm" class="px-4 py-2 border border-rule rounded-card text-sm text-ink2 hover:bg-bg">Cancel</button>
                    <button wire:click="save" class="px-4 py-2 bg-ink text-white text-sm font-bold rounded-card hover:opacity-90">{{ $editingId ? 'Update' : 'Add to Bank' }}</button>
                </div>
            </div>
        </div>
    @endif
</div>
