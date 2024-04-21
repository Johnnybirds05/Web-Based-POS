<template>
    <div>
        <h1>Hello world !!!</h1>
        <v-btn color="darkblueshade"
        prepend-icon="mdi-home">{{ greetings }}</v-btn>
        <v-icon icon="mdi-home" />
    </div>
</template>
<script>
     export default{
        data(){
            return {
                greetings: 'Hello World'
            };
        },
     }
</script>