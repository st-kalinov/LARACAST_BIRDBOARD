<template>
    <div class="dropdown dropdown-toggle relative">
        <div @click.prevent="isOpen = !isOpen">
            <slot name="trigger"></slot>
        </div>

        <div v-show="isOpen"
             class="dropdown-menu absolute bg-card py-2 rounded shadow mt-2"
             :class="align === 'left' ? 'pin-l' : 'pin-r'"
             :style="{ width }">


            <slot></slot>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Dropdown",
        props: {
            align: { default: 'left'},
            width: { default: 'auto' },
        },

        data() {
            return {
                isOpen: false,
            }
        },

        watch: {
            isOpen(isOpen) {
                if(isOpen) {
                    document.addEventListener('click', this.closeIfClickedOutside);
                }
            }
        },

        methods: {
            closeIfClickedOutside(event) {
                if( !event.target.closest('.dropdown')) {
                    this.isOpen = false;

                    document.removeEventListener('click', this.closeIfClickedOutside);
                }
            }
        }
    }
</script>

<style scoped>

</style>
