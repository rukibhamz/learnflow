<div class="max-w-3xl mx-auto">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="font-display font-extrabold text-2xl text-ink">{{ $quiz->title }}</h1>
        @if($quiz->description)
            <p class="text-sm text-ink2 mt-2">{{ $quiz->description }}</p>
        @endif
        <div class="flex flex-wrap gap-4 mt-4 text-xs text-ink3">
            <span>{{ $quiz->questions->count() }} questions</span>
            @if($quiz->time_limit_minutes)
                <span>{{ $quiz->time_limit_minutes }} min time limit</span>
            @endif
            <span>Pass: {{ $quiz->passing_score }}%</span>
            <span>Attempts: {{ $this->attemptsUsed }}/{{ $quiz->attempts_allowed ?? '∞' }}</span>
        </div>
    </div>

    {{-- Ready State --}}
    @if($state === 'ready')
        <div class="bg-surface border border-rule rounded-card p-8 text-center">
            <span class="material-symbols-outlined text-[48px] text-primary mb-4">quiz</span>
            <h2 class="font-display font-bold text-lg text-ink mb-2">Ready to begin?</h2>
            <p class="text-sm text-ink2 mb-6">You have {{ $quiz->questions->count() }} questions to answer.
                @if($quiz->time_limit_minutes)
                    Time limit: {{ $quiz->time_limit_minutes }} minutes.
                @endif
            </p>
            <button wire:click="startAttempt" class="px-8 py-3 bg-ink text-white font-display font-bold rounded-card hover:opacity-90 transition-opacity">
                Start Quiz
            </button>
        </div>
    @endif

    {{-- Taking State --}}
    @if($state === 'taking')
        <form wire:submit="submitQuiz" class="space-y-6">
            @foreach($this->questions as $qi => $question)
                <div wire:key="q-{{ $question->id }}" class="bg-surface border border-rule rounded-card p-6">
                    <div class="flex items-start gap-3 mb-4">
                        <span class="font-display font-bold text-primary text-sm mt-0.5">{{ $qi + 1 }}.</span>
                        <p class="text-sm text-ink font-medium flex-1">{{ $question->question }}</p>
                        <span class="text-[10px] text-ink3 shrink-0">{{ $question->points }} pt{{ $question->points > 1 ? 's' : '' }}</span>
                    </div>

                    @if($question->type->value === 'mcq')
                        <div class="space-y-2 ml-7">
                            @foreach($question->options ?? [] as $oi => $option)
                                <label class="flex items-center gap-3 p-3 rounded-lg border border-rule hover:bg-bg transition-colors cursor-pointer {{ ($answers[$question->id] ?? '') == (string) $oi ? 'bg-primary/5 border-primary/30' : '' }}">
                                    <input type="radio" wire:model="answers.{{ $question->id }}" value="{{ $oi }}" class="text-primary focus:ring-primary">
                                    <span class="text-sm text-ink">{{ $option }}</span>
                                </label>
                            @endforeach
                        </div>
                    @elseif($question->type->value === 'true_false')
                        <div class="flex gap-4 ml-7">
                            <label class="flex items-center gap-2 p-3 rounded-lg border border-rule hover:bg-bg transition-colors cursor-pointer flex-1 {{ ($answers[$question->id] ?? '') === 'true' ? 'bg-primary/5 border-primary/30' : '' }}">
                                <input type="radio" wire:model="answers.{{ $question->id }}" value="true" class="text-primary focus:ring-primary">
                                <span class="text-sm">True</span>
                            </label>
                            <label class="flex items-center gap-2 p-3 rounded-lg border border-rule hover:bg-bg transition-colors cursor-pointer flex-1 {{ ($answers[$question->id] ?? '') === 'false' ? 'bg-primary/5 border-primary/30' : '' }}">
                                <input type="radio" wire:model="answers.{{ $question->id }}" value="false" class="text-primary focus:ring-primary">
                                <span class="text-sm">False</span>
                            </label>
                        </div>
                    @elseif($question->type->value === 'short_answer')
                        <div class="ml-7">
                            <input type="text" wire:model="answers.{{ $question->id }}" class="w-full h-10 px-4 border border-rule rounded-card text-sm focus:outline-none focus:border-primary" placeholder="Type your answer...">
                        </div>
                    @endif
                </div>
            @endforeach

            <div class="flex justify-end pt-4">
                <button type="submit" wire:confirm="Submit your answers? You cannot change them after." class="px-8 py-3 bg-ink text-white font-display font-bold rounded-card hover:opacity-90 transition-opacity">
                    Submit Quiz
                </button>
            </div>
        </form>
    @endif

    {{-- Completed State --}}
    @if($state === 'completed' && $gradeResult)
        <div class="bg-surface border border-rule rounded-card p-8 mb-8 text-center">
            @if($gradeResult['passed'])
                <span class="material-symbols-outlined text-[48px] text-green-500 mb-3">check_circle</span>
                <h2 class="font-display font-bold text-xl text-green-700 mb-2">Passed!</h2>
            @else
                <span class="material-symbols-outlined text-[48px] text-red-400 mb-3">cancel</span>
                <h2 class="font-display font-bold text-xl text-red-700 mb-2">Not Passed</h2>
            @endif

            <p class="text-3xl font-display font-extrabold text-ink mb-1">{{ $gradeResult['score'] }}%</p>
            <p class="text-sm text-ink2">{{ $gradeResult['earned_points'] }} / {{ $gradeResult['total_points'] }} points</p>

            <div class="mt-6 flex justify-center gap-4">
                @if($quiz->attempts_allowed && $this->attemptsUsed < $quiz->attempts_allowed)
                    <button wire:click="retake" class="px-6 py-2.5 border border-rule rounded-card text-sm font-medium text-ink hover:bg-bg transition-colors">
                        Try Again ({{ $quiz->attempts_allowed - $this->attemptsUsed }} left)
                    </button>
                @endif
            </div>
        </div>

        {{-- Review Answers --}}
        @if($quiz->show_answers_after)
            <h3 class="font-display font-bold text-lg text-ink mb-4">Review</h3>
            <div class="space-y-4">
                @foreach($quiz->questions as $question)
                    @php $r = $gradeResult['results'][$question->id] ?? null; @endphp
                    <div class="bg-surface border rounded-card p-5 {{ $r && $r['correct'] ? 'border-green-200' : 'border-red-200' }}">
                        <div class="flex items-start gap-3 mb-2">
                            <span class="material-symbols-outlined text-[20px] {{ $r && $r['correct'] ? 'text-green-500' : 'text-red-500' }}">
                                {{ $r && $r['correct'] ? 'check_circle' : 'cancel' }}
                            </span>
                            <p class="text-sm text-ink font-medium flex-1">{{ $question->question }}</p>
                        </div>
                        <div class="ml-8 text-xs space-y-1">
                            @if($r)
                                @if($question->type->value === 'mcq')
                                    <p class="text-ink2">Your answer: <span class="font-medium">{{ $question->options[$r['student_answer']] ?? 'No answer' }}</span></p>
                                    @if(! $r['correct'])
                                        <p class="text-green-700">Correct: <span class="font-medium">{{ $question->options[$r['correct_answer']] ?? '' }}</span></p>
                                    @endif
                                @else
                                    <p class="text-ink2">Your answer: <span class="font-medium">{{ $r['student_answer'] ?: 'No answer' }}</span></p>
                                    @if(! $r['correct'])
                                        <p class="text-green-700">Correct: <span class="font-medium">{{ $r['correct_answer'] }}</span></p>
                                    @endif
                                @endif
                                @if($r['explanation'])
                                    <p class="text-ink3 mt-2 italic">{{ $r['explanation'] }}</p>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif

    {{-- No Retakes State --}}
    @if($state === 'no_retakes')
        <div class="bg-surface border border-rule rounded-card p-8 text-center">
            <span class="material-symbols-outlined text-[48px] text-ink3 mb-3">block</span>
            <h2 class="font-display font-bold text-lg text-ink mb-2">No Attempts Remaining</h2>
            <p class="text-sm text-ink2 mb-4">You've used all {{ $quiz->attempts_allowed }} allowed attempts.</p>

            @if($currentAttempt)
                <p class="text-2xl font-display font-extrabold text-ink">Last score: {{ $currentAttempt->score }}%</p>
                <p class="text-sm {{ $currentAttempt->passed ? 'text-green-600' : 'text-red-600' }} font-medium mt-1">
                    {{ $currentAttempt->passed ? 'Passed' : 'Not passed' }}
                </p>
            @endif
        </div>
    @endif
</div>
