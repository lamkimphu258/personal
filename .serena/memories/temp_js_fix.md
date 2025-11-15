    <script>
        function todoPage(config) {
            return {
                summary: config.summary ?? { total: 0, completed: 0, incomplete: 0 },
                contextDate: config.contextDate ?? null,
                isProcessing: false,
                async toggleOccurrence(payload) {
                    if (this.isProcessing || !payload?.url) {
                        return;
                    }

                    this.isProcessing = true;

                    try {
                        // Get the current wrapper element using a simpler selector
                        const wrapper = document.querySelector(`#occurrence-wrapper-${payload.id}`);
                        let currentCompleted = payload.completed;
                        
                        // Check the DOM for the current completed state
                        if (wrapper) {
                            const statusSpan = wrapper.querySelector('span[x-text*="completed"]');
                            if (statusSpan) {
                                currentCompleted = statusSpan.textContent.trim() === 'Completed';
                            }
                        }

                        const response = await axios.patch(payload.url, {
                            completed: !currentCompleted,
                        });

                        if (response?.data?.summary) {
                            this.summary = response.data.summary;
                        }

                        if (response?.data?.occurrence) {
                            window.dispatchEvent(new CustomEvent('todo-occurrence-updated', {
                                detail: {
                                    id: response.data.occurrence.id,
                                    completed: response.data.occurrence.is_completed,
                                },
                            }));
                        }
                    } catch (error) {
                        console.error('Unable to toggle task occurrence', error);
                        alert('Failed to update the task. Please try again.');
                    } finally {
                        this.isProcessing = false;
                    }
                },
            };
        }
    </script>