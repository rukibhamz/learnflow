<div>
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('instructor.courses.curriculum', $lesson->section->course) }}" class="text-ink3 hover:text-ink transition-colors">
                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                </a>
                <h1 class="font-display font-extrabold text-2xl text-ink">Edit Lesson</h1>
            </div>
            <p class="text-sm text-ink3">{{ $lesson->section->course->title }} → {{ $lesson->section->title }}</p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" wire:click="save" class="px-4 py-2 border border-rule rounded-lg text-sm font-medium text-ink2 hover:bg-bg transition-colors">
                Save
            </button>
            <button type="button" wire:click="saveAndReturn" class="px-5 py-2 bg-ink text-white font-display font-bold text-sm rounded-lg hover:opacity-90 transition-opacity">
                Save & Return
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Basic Info --}}
            <div class="bg-surface border border-rule rounded-lg p-6 space-y-4">
                <h3 class="font-display font-bold text-sm text-ink uppercase tracking-widest">Lesson Details</h3>
                
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Title *</label>
                    <input type="text" wire:model="title" 
                        class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary" 
                        placeholder="Lesson title" required>
                    @error('title') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center gap-4">
                    @php
                        $typeLabels = [
                            'video' => ['play_circle', 'Video', 'text-blue-500'],
                            'text' => ['article', 'Text', 'text-green-500'],
                            'pdf' => ['picture_as_pdf', 'PDF', 'text-red-500'],
                            'embed' => ['code', 'Embed', 'text-purple-500'],
                        ];
                        $typeInfo = $typeLabels[$type] ?? ['description', 'Unknown', 'text-ink3'];
                    @endphp
                    <span class="material-symbols-outlined text-[24px] {{ $typeInfo[2] }}">{{ $typeInfo[0] }}</span>
                    <span class="text-sm font-medium text-ink">{{ $typeInfo[1] }} Lesson</span>
                </div>
            </div>

            {{-- Content Based on Type --}}
            @if($type === 'video')
                <div class="bg-surface border border-rule rounded-lg p-6 space-y-4">
                    <h3 class="font-display font-bold text-sm text-ink uppercase tracking-widest">Video Content</h3>
                    
                    {{-- Video URL Input --}}
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Video URL</label>
                        <input type="url" wire:model="content_url" 
                            class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary" 
                            placeholder="https://youtube.com/watch?v=... or https://vimeo.com/...">
                        <p class="text-xs text-ink3 mt-1">Supports YouTube, Vimeo, or direct video URLs</p>
                        @error('content_url') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- S3 Upload --}}
                    <div class="border-t border-rule pt-4">
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Or Upload Video</label>
                        <div x-data="{
                            uploading: false,
                            progress: 0,
                            async uploadToS3(file) {
                                if (!file) return;
                                this.uploading = true;
                                this.progress = 0;
                                
                                try {
                                    const presigned = await $wire.getPresignedUrl();
                                    
                                    const xhr = new XMLHttpRequest();
                                    xhr.upload.addEventListener('progress', (e) => {
                                        if (e.lengthComputable) {
                                            this.progress = Math.round((e.loaded / e.total) * 100);
                                        }
                                    });
                                    
                                    await new Promise((resolve, reject) => {
                                        xhr.onload = () => xhr.status === 200 ? resolve() : reject(new Error('Upload failed'));
                                        xhr.onerror = () => reject(new Error('Upload failed'));
                                        xhr.open('PUT', presigned.url);
                                        xhr.setRequestHeader('Content-Type', 'video/mp4');
                                        xhr.send(file);
                                    });
                                    
                                    const videoUrl = presigned.url.split('?')[0];
                                    await $wire.setVideoUrl(videoUrl);
                                } catch (error) {
                                    alert('Upload failed: ' + error.message);
                                } finally {
                                    this.uploading = false;
                                }
                            }
                        }">
                            <div class="border-2 border-dashed border-rule rounded-lg p-6 text-center"
                                 x-on:dragover.prevent="$el.classList.add('border-primary', 'bg-primary/5')"
                                 x-on:dragleave.prevent="$el.classList.remove('border-primary', 'bg-primary/5')"
                                 x-on:drop.prevent="$el.classList.remove('border-primary', 'bg-primary/5'); uploadToS3($event.dataTransfer.files[0])">
                                
                                <template x-if="!uploading">
                                    <div>
                                        <span class="material-symbols-outlined text-[48px] text-ink3 mb-2">cloud_upload</span>
                                        <p class="text-sm text-ink3 mb-3">Drag & drop video file or click to browse</p>
                                        <input type="file" accept="video/mp4,video/webm" class="hidden" x-ref="videoInput"
                                               @change="uploadToS3($event.target.files[0])">
                                        <button type="button" @click="$refs.videoInput.click()" 
                                            class="px-4 py-2 bg-bg border border-rule rounded-lg text-sm font-medium text-ink2 hover:bg-background-light transition-colors">
                                            Choose Video
                                        </button>
                                    </div>
                                </template>
                                
                                <template x-if="uploading">
                                    <div>
                                        <div class="w-full bg-bg rounded-full h-3 mb-3">
                                            <div class="bg-primary h-3 rounded-full transition-all" :style="'width: ' + progress + '%'"></div>
                                        </div>
                                        <p class="text-sm text-ink2">Uploading... <span x-text="progress"></span>%</p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Duration --}}
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Duration (seconds)</label>
                        <input type="number" wire:model="duration_seconds" min="0"
                            class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary" 
                            placeholder="e.g. 600 for 10 minutes">
                        @error('duration_seconds') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

            @elseif($type === 'text')
                <div class="bg-surface border border-rule rounded-lg p-6 space-y-4">
                    <h3 class="font-display font-bold text-sm text-ink uppercase tracking-widest">Text Content</h3>
                    
                    <div x-data="{ 
                        content: @entangle('content_body'),
                        init() {
                            const quill = new Quill(this.$refs.editor, {
                                theme: 'snow',
                                placeholder: 'Write your lesson content here...',
                                modules: {
                                    toolbar: [
                                        [{ 'header': [1, 2, 3, false] }],
                                        ['bold', 'italic', 'underline', 'strike'],
                                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                        ['blockquote', 'code-block'],
                                        ['link', 'image'],
                                        ['clean']
                                    ]
                                }
                            });
                            quill.root.innerHTML = this.content;
                            quill.on('text-change', () => {
                                this.content = quill.root.innerHTML;
                            });
                        }
                    }">
                        <div x-ref="editor" class="bg-bg border border-rule rounded-lg min-h-[400px]"></div>
                    </div>
                    @error('content_body') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

            @elseif($type === 'pdf')
                <div class="bg-surface border border-rule rounded-lg p-6 space-y-4">
                    <h3 class="font-display font-bold text-sm text-ink uppercase tracking-widest">PDF Document</h3>
                    
                    @if($existingPdf)
                        <div class="flex items-center gap-4 p-4 bg-bg rounded-lg">
                            <span class="material-symbols-outlined text-[32px] text-red-500">picture_as_pdf</span>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-ink">Current PDF</p>
                                <a href="{{ $existingPdf }}" target="_blank" class="text-xs text-primary hover:underline">View PDF</a>
                            </div>
                            <button type="button" wire:click="removePdf" class="p-2 text-ink3 hover:text-red-500 transition-colors">
                                <span class="material-symbols-outlined text-[20px]">delete</span>
                            </button>
                        </div>
                    @endif

                    <div class="border-2 border-dashed border-rule rounded-lg p-6 text-center"
                         x-data="{ dragging: false }"
                         x-on:dragover.prevent="dragging = true"
                         x-on:dragleave.prevent="dragging = false"
                         x-on:drop.prevent="dragging = false; $refs.pdfInput.files = $event.dataTransfer.files; $refs.pdfInput.dispatchEvent(new Event('change'))"
                         :class="{ 'border-primary bg-primary/5': dragging }">
                        
                        @if($pdfFile)
                            <div class="flex items-center justify-center gap-3">
                                <span class="material-symbols-outlined text-[32px] text-red-500">picture_as_pdf</span>
                                <span class="text-sm text-ink">{{ $pdfFile->getClientOriginalName() }}</span>
                                <button type="button" wire:click="$set('pdfFile', null)" class="p-1 text-ink3 hover:text-red-500">
                                    <span class="material-symbols-outlined text-[18px]">close</span>
                                </button>
                            </div>
                        @else
                            <span class="material-symbols-outlined text-[48px] text-ink3 mb-2">upload_file</span>
                            <p class="text-sm text-ink3 mb-3">Drag & drop PDF or click to browse</p>
                        @endif
                        
                        <input type="file" wire:model="pdfFile" accept="application/pdf" class="hidden" x-ref="pdfInput" id="pdf-input">
                        <label for="pdf-input" class="inline-block px-4 py-2 bg-bg border border-rule rounded-lg text-sm font-medium text-ink2 cursor-pointer hover:bg-background-light transition-colors">
                            {{ $pdfFile ? 'Change PDF' : 'Choose PDF' }}
                        </label>
                        
                        <div wire:loading wire:target="pdfFile" class="mt-2 text-xs text-primary">Uploading...</div>
                    </div>
                    @error('pdfFile') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

            @elseif($type === 'embed')
                <div class="bg-surface border border-rule rounded-lg p-6 space-y-4">
                    <h3 class="font-display font-bold text-sm text-ink uppercase tracking-widest">Embed Content</h3>
                    
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Embed URL</label>
                        <input type="url" wire:model="content_url" 
                            class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary" 
                            placeholder="https://codepen.io/... or https://codesandbox.io/...">
                        <p class="text-xs text-ink3 mt-1">Supports CodePen, CodeSandbox, Figma, and other embeddable content</p>
                        @error('content_url') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Settings --}}
            <div class="bg-surface border border-rule rounded-lg p-6 space-y-4">
                <h3 class="font-display font-bold text-sm text-ink uppercase tracking-widest">Settings</h3>
                
                {{-- Free Preview Toggle --}}
                <label class="flex items-center gap-3 cursor-pointer">
                    <div class="relative">
                        <input type="checkbox" wire:model="is_preview" class="sr-only peer">
                        <div class="w-11 h-6 bg-bg border border-rule rounded-full peer-checked:bg-primary transition-colors"></div>
                        <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-ink">Free Preview</span>
                        <p class="text-xs text-ink3">Allow non-enrolled users to view</p>
                    </div>
                </label>

                {{-- Drip Content --}}
                <div class="border-t border-rule pt-4">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Drip Schedule</label>
                    <div class="flex items-center gap-2">
                        <input type="number" wire:model="unlock_after_days" min="0" 
                            class="w-24 h-10 bg-bg border border-rule rounded-lg px-3 text-sm focus:outline-none focus:border-primary" 
                            placeholder="0">
                        <span class="text-sm text-ink3">days after enrollment</span>
                    </div>
                    <p class="text-xs text-ink3 mt-1">Leave empty for immediate access</p>
                    @error('unlock_after_days') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Quiz Builder --}}
            <div class="bg-surface border border-rule rounded-lg p-6 space-y-3">
                <h3 class="font-display font-bold text-sm text-ink uppercase tracking-widest">Quiz</h3>
                <p class="text-xs text-ink3">Add or edit a quiz for this lesson. Students can take it after completing the lesson content.</p>
                <a href="{{ route('instructor.lessons.quiz', $lesson) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-ink text-white font-display font-bold text-sm rounded-lg hover:opacity-90 transition-opacity">
                    <span class="material-symbols-outlined text-[18px]">quiz</span>
                    {{ $lesson->quiz ? 'Edit Quiz' : 'Add Quiz' }}
                </a>
            </div>

            {{-- Quick Info --}}
            <div class="bg-surface border border-rule rounded-lg p-6 space-y-3">
                <h3 class="font-display font-bold text-sm text-ink uppercase tracking-widest">Info</h3>
                <div class="text-xs text-ink3 space-y-2">
                    <p><strong>Section:</strong> {{ $lesson->section->title }}</p>
                    <p><strong>Order:</strong> #{{ $lesson->order }}</p>
                    <p><strong>Created:</strong> {{ $lesson->created_at->format('M j, Y') }}</p>
                    @if($lesson->updated_at->ne($lesson->created_at))
                        <p><strong>Updated:</strong> {{ $lesson->updated_at->format('M j, Y H:i') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
    .ql-container { font-family: inherit; font-size: 14px; }
    .ql-editor { min-height: 350px; }
    .ql-toolbar { border-radius: 8px 8px 0 0; border-color: var(--rule) !important; background: var(--bg); }
    .ql-container { border-radius: 0 0 8px 8px; border-color: var(--rule) !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
@endpush
