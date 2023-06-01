<script setup lang="ts">

import FrontLayout from '@/Layouts/FrontLayout.vue';

import { onMounted, ref } from 'vue';
import { Link, Head, useForm } from '@inertiajs/vue3';

import * as VotingTypes from '@/types/voting-types';


onMounted(() => {
    console.log('Index.vue mounted');


});



const form = useForm({
    name: "New Election",
    description: "New Election description",
    credits: 100,
    motions: [] as VotingTypes.Motion[],
    options: {}
});


const createUUID = () => {
    console.log('createUUID');

    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
        var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
};


</script>


<template>
    <Head title="Create Motion" />

    <FrontLayout>

        <!-- <pre>
            {{ form.errors }}
        </pre>

        <pre>
            {{ form }}
        </pre> -->


          <form @submit.prevent="form.post(route('election.store'))">

            <label for="name">Name</label>
            <input type="text" id="name" v-model="form.name" />
            <div class="error" v-if="form.errors.name">{{ form.errors.name }}</div>

            <label for="description">Description</label>
            <textarea id="description" v-model="form.description"></textarea>
            <div class="error" v-if="form.errors.description">{{ form.errors.description }}</div>


            <label for="credits">Credits</label>
            <input type="number" id="credits" v-model="form.credits" />
            <div class="error" v-if="form.errors.credits">{{ form.errors.credits }}</div>

            <label for="motions">Motions</label>
            <div class="error" v-if="form.errors.motions">{{ form.errors.motions }}</div>
            <div class="motions-form">

                <div class="motion" v-for="(motion, index) in form.motions" :key="index">
                    <input type="text" v-model="form.motions[index].content" />

                    <button
                        @click.prevent="() => form.motions.splice(index - 1, 0, form.motions.splice(index, 1)[0])">Up</button>
                    <button
                        @click.prevent="() => form.motions.splice(index + 1, 0, form.motions.splice(index, 1)[0])">Down</button>
                    <button @click.prevent="form.motions.splice(index, 1)">Remove</button>

                </div>

                <button @click.prevent="form.motions.push({
                    content: `Motion ${form.motions.length + 1}`,
                    votes: 0,
                    uuid: createUUID(),
                    credits: 0,
                    })">Add Motion</button>
            </div>



            <button type="submit">Create</button>



        </form>




    </FrontLayout>
</template>



<style scoped>
form {

    display: flex;
    flex-direction: column;
    gap: 1rem;

    padding: 3rem;


}

.error {
    color: red;
}

.motions {
    display: grid;
    /* auto columns max with */

    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;

}

.motion {
    padding: 2rem;

    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
    align-items: flex-start;

    gap: 10px;
}

button {
    padding: 1rem;
    background-color: beige;
}
</style>
