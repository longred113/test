<template>
    <div>
        <h1>World Chat</h1>
        <div>
            <div v-for="message in messages" :key="message.id">
                <strong>{{ message.user }}</strong>: {{ message.content }}
            </div>
        </div>
        <div>
            <form @submit.prevent="sendMessage">
                <input type="text" v-model="newMessage" placeholder="Type your message...">
                <button type="submit">Send</button>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                messages: [],
                newMessage: ''
            }
        },
        mounted() {
            Echo.channel('chat-room')
                .listen('.chat-message-sent', (e) => {
                    this.messages.push({
                        id: e.message.id,
                        content: e.message.content,
                        user: e.user.name
                    });
                });
        },
        methods: {
            sendMessage() {
                axios.post('/api/messages', {
                    content: this.newMessage
                })
                .then(response => {
                    this.newMessage = '';
                });
            }
        }
    }
</script>