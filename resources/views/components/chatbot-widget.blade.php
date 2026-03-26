<div
    x-data="shopAssistantWidget(@js(route('chatbot.message')), @js(csrf_token()), @js(__('ui.chatbot_error')))"
    class="fixed bottom-4 right-4 z-50"
    style="position: fixed; bottom: 16px; right: 16px; z-index: 9999;"
>
    <button
        type="button"
        @click="toggle()"
        class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-primary-600 text-white shadow-lg transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
        style="width: 56px; height: 56px; border-radius: 9999px; background: #2563eb; color: #fff; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25); display: inline-flex; align-items: center; justify-content: center;"
        aria-label="{{ __('ui.chat_assistant') }}"
    >
        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4l-4 4v-4z" />
        </svg>
    </button>

    <div
        x-show="open"
        x-transition
        @click.away="open = false"
        x-cloak
        class="absolute bottom-16 right-0 flex w-[22rem] max-w-[92vw] flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-2xl dark:border-gray-700 dark:bg-gray-800"
        style="display: none; position: fixed; right: 16px; bottom: 84px; width: min(22rem, calc(100vw - 20px)); height: min(32rem, calc(100vh - 120px)); z-index: 10000;"
    >
        <div class="flex items-center justify-between bg-primary-600 px-4 py-3 text-white">
            <h3 class="text-sm font-semibold">{{ __('ui.chat_assistant') }}</h3>
            <button type="button" @click="open = false" class="rounded p-1 hover:bg-primary-700" aria-label="Close">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div x-ref="messageBox" class="flex-1 min-h-0 space-y-3 overflow-y-auto bg-gray-50 p-4 dark:bg-gray-900">
            <template x-for="(message, index) in messages" :key="index">
                <div>
                    <div :class="message.role === 'user' ? 'ml-8 rounded-xl bg-primary-600 p-3 text-sm text-white' : 'mr-8 rounded-xl bg-white p-3 text-sm text-gray-800 shadow dark:bg-gray-800 dark:text-gray-100'">
                        <p class="whitespace-pre-line" x-text="message.text"></p>
                    </div>

                    <template x-if="message.products && message.products.length">
                        <div class="mr-8 mt-2 space-y-2">
                            <template x-for="product in message.products" :key="product.id">
                                <a :href="product.url" class="block rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-700 transition hover:border-primary-300 hover:bg-primary-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                                    <div class="font-semibold" x-text="product.name"></div>
                                    <div class="mt-1 flex items-center justify-between">
                                        <span x-text="product.price"></span>
                                        <span :class="product.in_stock ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'" x-text="product.in_stock ? 'In stock' : 'Out of stock'"></span>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </template>
                </div>
            </template>

            <template x-if="isLoading">
                <div class="mr-8 rounded-xl bg-white p-3 text-sm text-gray-500 shadow dark:bg-gray-800 dark:text-gray-300">
                    {{ __('ui.chatbot_thinking') }}
                </div>
            </template>
        </div>

        <form @submit.prevent="sendMessage()" class="flex gap-2 border-t border-gray-200 p-3 dark:border-gray-700">
            <input
                x-model="input"
                type="text"
                maxlength="800"
                class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                placeholder="{{ __('ui.chatbot_placeholder') }}"
            >
            <button
                type="submit"
                :disabled="isLoading || !input.trim()"
                class="rounded-lg bg-primary-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-primary-700 disabled:cursor-not-allowed disabled:opacity-60"
            >
                {{ __('ui.chatbot_send') }}
            </button>
        </form>
    </div>
</div>

@once
    <script>
        function shopAssistantWidget(endpoint, csrfToken, defaultError) {
            return {
                open: false,
                input: '',
                isLoading: false,
                messages: [
                    {
                        role: 'assistant',
                        text: @js(__('ui.chatbot_welcome')),
                        products: [],
                    },
                ],

                toggle() {
                    this.open = !this.open;

                    if (this.open) {
                        this.$nextTick(() => this.scrollToBottom());
                    }
                },

                async sendMessage() {
                    const text = this.input.trim();

                    if (!text || this.isLoading) {
                        return;
                    }

                    this.messages.push({
                        role: 'user',
                        text,
                        products: [],
                    });

                    this.input = '';
                    this.isLoading = true;
                    this.$nextTick(() => this.scrollToBottom());

                    try {
                        const response = await fetch(endpoint, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({ message: text }),
                        });

                        if (!response.ok) {
                            throw new Error('Chatbot request failed with status ' + response.status);
                        }

                        const data = await response.json();

                        this.messages.push({
                            role: 'assistant',
                            text: data.reply || defaultError,
                            products: Array.isArray(data.products) ? data.products : [],
                        });
                    } catch (error) {
                        this.messages.push({
                            role: 'assistant',
                            text: defaultError,
                            products: [],
                        });
                    } finally {
                        this.isLoading = false;
                        this.$nextTick(() => this.scrollToBottom());
                    }
                },

                scrollToBottom() {
                    const box = this.$refs.messageBox;

                    if (!box) {
                        return;
                    }

                    box.scrollTop = box.scrollHeight;
                },
            };
        }
    </script>
@endonce
