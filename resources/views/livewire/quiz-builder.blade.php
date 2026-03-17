<div>
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- Settings --}}
    <div class="bg-surface border border-rule rounded-card p-6 mb-6 space-y-4">
        <h2 class="font-display font-bold text-ink">Quiz Settings</h2>

        <div class="space-y-4">
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Title *</label>
                <input type="text" wire:model="title" class="w-full h-10 px-4 border border-rule rounded-card text-sm focus:outline-none focus:border-primary" placeholder="Quiz title">
                @error('title') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Description</label>
                <textarea wire:model="description" rows="2" class="w-full px-4 py-2 border border-rule rounded-card text-sm focus:outline-none focus:border-primary resize-none" placeholder="Optional description"></textarea>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Time limit (min)</label>
                    <input type="number" wire:model="time_limit_minutes" min="1" max="300" class="w-full h-9 px-3 border border-rule rounded-card text-sm">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Attempts allowed</label>
                    <input type="number" wire:model="attempts_allowed" min="1" max="100" class="w-full h-9 px-3 border border-rule rounded-card text-sm">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Passing score %</label>
                    <input type="number" wire:model="passing_score" min="0" max="100" class="w-full h-9 px-3 border border-rule rounded-card text-sm">
                </div>
                <div class="flex items-end gap-4 pb-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="shuffle_questions" class="rounded border-rule text-primary focus:ring-primary">
                        <span class="text-xs text-ink2">Shuffle</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="show_answers_after" class="rounded border-rule text-primary focus:ring-primary">
                        <span class="text-xs text-ink2">Show answers</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    {{-- Questions --}}
    @foreach($questions as $i => $q)
    <div wire:key="question-{{ $i }}" class="bg-surface border border-rule rounded-card p-6 mb-4">
        <div class="flex items-center gap-3 mb-4">
            <span class="font-display font-bold text-primary text-sm">Q{{ $i + 1 }}</span>

            <select wire:model.live="questions.{{ $i }}.type" class="h-8 px-3 border border-rule rounded-card text-xs bg-surface focus:outline-none focus:border-primary">
                <option value="mcq">Multiple Choice</option>
                <option value="true_false">True / False</option>
                <option value="short_answer">Short Answer</option>
            </select>

            <div class="ml-auto flex items-center gap-2">
                <input type="number" wire:model="questions.{{ $i }}.points" min="1" class="w-16 h-8 px-2 border border-rule rounded-card text-xs text-right">
                <span class="text-xs text-ink3">pts</span>
                <button type="button" wire:click="removeQuestion({{ $i }})" wire:confirm="Remove this question?" class="ml-2 p-1 text-ink3 hover:text-red-600 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                </button>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Question *</label>
            <textarea wire:model="questions.{{ $i }}.question" rows="2" class="w-full px-4 py-2 border border-rule rounded-card text-sm focus:outline-none focus:border-primary resize-none" placeholder="Enter your question"></textarea>
            @error("questions.{$i}.question") <span class="text-xs text-red-600">{{ $message }}</span> @enderror
        </div>

        @if($q['type'] === 'mcq')
            <div class="space-y-2 mb-3">
                @foreach($q['options'] ?? [] as $oi => $opt)
                <div class="flex items-center gap-2">
                    <input type="radio" wire:model="questions.{{ $i }}.correct_answer" value="{{ $oi }}" class="text-primary focus:ring-primary">
                    <input type="text" wire:model="questions.{{ $i }}.options.{{ $oi }}" class="flex-1 h-9 px-3 border border-rule rounded-card text-sm focus:outline-none focus:border-primary" placeholder="Option {{ $oi + 1 }}">
                    @if(count($q['options']) > 2)
                        <button type="button" wire:click="removeOption({{ $i }}, {{ $oi }})" class="p-1 text-ink3 hover:text-red-600">
                            <span class="material-symbols-outlined text-[16px]">close</span>
                        </button>
                    @endif
                </div>
                @endforeach
                <button type="button" wire:click="addOption({{ $i }})" class="text-primary text-xs font-medium hover:underline">+ Add option</button>
            </div>
        @elseif($q['type'] === 'true_false')
            <div class="flex gap-4 mb-3">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" wire:model="questions.{{ $i }}.correct_answer" value="true" class="text-primary focus:ring-primary">
                    <span class="text-sm">True</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" wire:model="questions.{{ $i }}.correct_answer" value="false" class="text-primary focus:ring-primary">
                    <span class="text-sm">False</span>
                </label>
            </div>
        @elseif($q['type'] === 'short_answer')
            <div class="mb-3">
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Correct answer *</label>
                <input type="text" wire:model="questions.{{ $i }}.correct_answer" class="w-full h-9 px-3 border border-rule rounded-card text-sm focus:outline-none focus:border-primary" placeholder="Expected answer (case-insensitive)">
            </div>
        @endif

        @error("questions.{$i}.correct_answer") <span class="text-xs text-red-600">{{ $message }}</span> @enderror

        <div>
            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Explanation (optional)</label>
            <input type="text" wire:model="questions.{{ $i }}.explanation" class="w-full h-9 px-3 border border-rule rounded-card text-sm focus:outline-none focus:border-primary" placeholder="Shown after submission">
        </div>
    </div>
    @endforeach

    <button type="button" wire:click="addQuestion" class="w-full py-3 border-2 border-dashed border-rule rounded-card text-ink3 text-sm hover:border-primary hover:text-primary transition-colors mb-6">
        + Add Question
    </button>

    {{-- Actions --}}
    <div class="flex items-center justify-between">
        @if($quiz)
            <button type="button" wire:click="deleteQuiz" wire:confirm="Delete this quiz and all questions?" class="text-sm text-red-600 hover:underline">
                Delete Quiz
            </button>
        @else
            <div></div>
        @endif

        <button type="button" wire:click="save" class="px-6 py-2.5 bg-ink text-white font-display font-bold text-sm rounded-card hover:opacity-90 transition-opacity">
            {{ $quiz ? 'Save Changes' : 'Create Quiz' }}
        </button>
    </div>
</div>
