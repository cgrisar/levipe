<template>
<div>
    <a class="text-black font-regular uppercase no-underline"
        @click="$modal.show('login-modal')"
    >
        <i class="spk-icon-user-account text-xl"></i>
    </a>
    <modal 
        name="login-modal"
        classes="py-8 px-8 bg-white shadow-lg"
        height="auto"
    >
        <div>
            <div class="flex flex-col text-center">
                <img src="/site/themes/gust/images/logo_levipe.png" class="h-14 w-auto lg:h-24 lg:w-auto mb-6 mx-auto" />
                <div id="connexion">
                    <h4 class="uppercase mb-6">Login</h4>
                    <div class="flex flex-col mx-auto">
                        <form autocomplete="off"
                            @submit.prevent="loginUser"
                            @keydown="submitted = false"
                        >
                            <div class="mb-2 w-4/5 flex mx-auto lg:flex-row lg:justify-between">
                                <div class="flex flex-1 flex-col">
                                    <input class="bg-grey-light appearance-none rounded-lg py-2 px-4 text-black focus:outline-none" 
                                            name="username"
                                            id="username" 
                                            type="email"
                                            v-model="message.username"
                                            placeholder="email"
                                            @keydown="delete errors.username">
                                    <span class="text-red text-xs text-left ml-4"
                                            v-text="errors.username[0]"
                                            v-if="errors.username"></span>
                                </div>
                                <div class="px-4"></div>
                                <div class="flex flex-1 flex-col">
                                    <input class="bg-grey-light appearance-none rounded-lg py-2 px-4 text-black focus:outline-none" 
                                            name="password"
                                            id="password"
                                            type="password"
                                            v-model="message.password"
                                            placeholder="password"
                                            @keydown="delete errors.password">
                                    <span class="text-red text-xs text-left ml-4"
                                            v-text="errors.password[0]"
                                            v-if="errors.password"></span>
                                </div>
                            </div>
                            <div class="flex flex-col">
                                <div class="flex justify-between w-4/5 mx-auto px-1 items-center mb-6">
                                    <span class="text-xs font-medium underline" onclick="toggle_connexions()">Init password</span>
                                    <button type="submit" :disabled="submitted">
                                            <span class="fa-fw fa-layers fa-2x -mr-2" title="Sign In">
                                                <i class="fas fa-circle text-green-darker"></i>
                                                <i class="fas fa-sign-in-alt fa-inverse" data-fa-transform="shrink-6"></i>
                                            </span>
                                    </button>
                                </div>
                                <div>
                                    <a href="/register">
                                        <h4 class="text-black underline uppercase">Create account</h4>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </modal>
</div>
</template>

<script>
export default {

    props: ['csrf', 'currenturl'],

    data() {
        return {
            message: {},
            errors: {},
            submited: false
        }
    },

    methods: {
        loginUser() {
            this.submitted = true;
            this.message._token = this.csrf;
            this.axios
                .post('/!/User/loginModal', this.message )
                .catch( (errors) => {
                    this.errors = errors.response.data.errors;
                });
        },

        toggle_connexions() {
            document.getElementById('connexion').classList.toggle('hidden');
            document.getElementById('lostpassword').classList.toggle('hidden');
        }
    }
}
</script>
