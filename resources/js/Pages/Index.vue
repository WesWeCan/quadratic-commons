<script setup lang="ts">

import FrontLayout from '@/Layouts/FrontLayout.vue';

import { onMounted, ref } from 'vue';
import { Link, Head } from '@inertiajs/vue3';




onMounted(() => {
    console.log('Index.vue mounted');


});




</script>


<template>

    <Head title="Home" />


    <FrontLayout>



        <section class="tutorial">
            <article>
                <h1>Quadratic Commons</h1>
                <p>Quadratic Voting is a collective decision-making process that allows you to express the intensity of
                    your preferences on a number of issues. Instead of a typical 'A/B' vote, you will spread your votes
                    across different issues to exercise more voting power. The more you spread your votes, the more
                    voting power you exercise, thanks to the quadratic mathematics behind the process. This encourages
                    all voters to compromise and ensures that the group's preferences are accurately represented.</p>
            </article>


            <article>
                <h3>Creating a Voting Round</h3>
                <p>To create a new voting round, simply click on the 'Create Voting Round' button on the main page. You
                    will be prompted to enter the details of the voting round, including the issues to be voted on, the
                    number of credits each voter will receive. Once you have entered all the details, click 'Create' to
                    create the voting round and share the link.</p>
            </article>

            


            <article>
                <h3>Let's Get Started</h3>
                <p>Now that you understand how Quadratic Voting works and how to create or join a voting round, it's
                    time to get started!</p>
                <br />
                <Link :href="route('election.create')" as="button">Create voting round</Link>
            </article>
        </section>

        <section class="tutorial">
        <article>
                <h2>About Charging the Commons</h2>
                <p>Charging the Commons is a 2-year project that investigates the design of digital platforms for
                    resource communities. The project explores how a Situated-design approach can be employed to
                    articulate the (social) values of resource communities. In addition, the project examines how these
                    values can be translated into a design for the management of an urban commons. </p>

                <p>This quadratic voting prototype was created for the workshop Design for collective decision-making is
                    part of the research project Charging the Commons, a cooperation between Amsterdam University of
                    Applied Sciences (Civic Interaction Design) and Avans University of Applied Sciences (Situated Art &
                    Design)</p>

                <div class="ratio-16-9">
                    <iframe :src="`https://www.youtube.com/embed/n0CEpT7ZOEo`" frameborder="0"
                        allow="autoplay; encrypted-media" allowfullscreen="true" class="ratio-inner"></iframe>
                </div>
            </article>
        </section>






    </FrontLayout>

</template>
