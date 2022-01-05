<style lang="scss" scoped>
.item-list {
  &:hover:first-child {
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
  }

  &:last-child {
    border-bottom: 0;
  }

  &:hover:last-child {
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
  }
}
</style>

<template>
  <layout :layout-data="layoutData">
    <!-- breadcrumb -->
    <nav class="sm:border-b bg-white">
      <div class="max-w-8xl mx-auto px-4 sm:px-6 py-2 hidden md:block">
        <div class="flex items-baseline justify-between space-x-6">
          <ul class="text-sm">
            <li class="inline mr-2 text-gray-600">You are here:</li>
            <li class="inline mr-2">
              <inertia-link :href="data.url.settings" class="text-sky-500 hover:text-blue-900">Settings</inertia-link>
            </li>
            <li class="inline mr-2 relative">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline relative icon-breadcrumb" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </li>
            <li class="inline mr-2"><inertia-link :href="data.url.personalize" class="text-sky-500 hover:text-blue-900">Personalize your account</inertia-link></li>
            <li class="inline mr-2 relative">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline relative icon-breadcrumb" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </li>
            <li class="inline">Contact information types</li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="sm:mt-20 relative">
      <div class="max-w-3xl mx-auto px-2 py-2 sm:py-6 sm:px-6 lg:px-8">
        <!-- title + cta -->
        <div class="sm:flex items-center justify-between mb-6 sm:mt-0 mt-8">
          <h3 class="mb-4 sm:mb-0">
            <span class="mr-1">
              ☎️
            </span> All the contact information types
          </h3>
          <pretty-button v-if="!createContactInformationTypeModalShown" :text="'Add a type'" :icon="'plus'" @click="showContactInformationTypeModal" />
        </div>

        <!-- modal to create a new contact information type -->
        <form v-if="createContactInformationTypeModalShown" class="bg-white border border-gray-200 rounded-lg mb-6" @submit.prevent="submit()">
          <div class="p-5 border-b border-gray-200">
            <errors :errors="form.errors" />

            <text-input :ref="'newContactInformationType'"
                        v-model="form.name"
                        :label="'Name'" :type="'text'"
                        :autofocus="true"
                        :input-class="'block w-full mb-3'"
                        :required="true"
                        :autocomplete="false"
                        :maxlength="255"
                        @esc-key-pressed="createContactInformationTypeModalShown = false"
            />

            <text-input v-model="form.protocol"
                        :label="'Protocol'" :type="'text'"
                        :autofocus="true"
                        :input-class="'block w-full'"
                        :required="false"
                        :autocomplete="false"
                        :maxlength="255"
                        :help="'A contact information can be clickable. For instance, a phone number can be clickable and we will launch the default application in your computer associated with a phone number. If you do not know the protocol for the type you are adding, you can simply omit this field.'"
                        @esc-key-pressed="createContactInformationTypeModalShown = false"
            />
          </div>

          <div class="p-5 flex justify-between">
            <pretty-span :text="'Cancel'" :classes="'mr-3'" @click="createContactInformationTypeModalShown = false" />
            <pretty-button :text="'Add the type'" :state="loadingState" :icon="'plus'" :classes="'save'" />
          </div>
        </form>

        <!-- list of groups types -->
        <ul v-if="localContactInformationTypes.length > 0" class="bg-white border border-gray-200 rounded-lg mb-6">
          <li v-for="contactInformationType in localContactInformationTypes" :key="contactInformationType.id" class="border-b border-gray-200 hover:bg-slate-50 item-list">
            <!-- detail of the group type -->
            <div v-if="renameContactInformationTypeModalShownId != contactInformationType.id" class="flex justify-between items-center px-5 py-2">
              <div>
                <span class="text-base">{{ contactInformationType.name }}</span>
                <code v-if="contactInformationType.protocol" class="text-xs code ml-3">[Protocol: {{ contactInformationType.protocol }}]</code>
              </div>

              <!-- actions -->
              <ul class="text-sm">
                <li class="cursor-pointer inline text-sky-500 hover:text-blue-900" @click="updateAdressTypeModal(contactInformationType)">Rename</li>
                <li v-if="contactInformationType.can_be_deleted" class="ml-4 cursor-pointer inline text-red-500 hover:text-red-900" @click="destroy(contactInformationType)">Delete</li>
              </ul>
            </div>

            <!-- rename a contactInformationType modal -->
            <form v-if="renameContactInformationTypeModalShownId == contactInformationType.id" class="border-b border-gray-200 hover:bg-slate-50 item-list" @submit.prevent="update(contactInformationType)">
              <div class="p-5 border-b border-gray-200">
                <errors :errors="form.errors" />

                <text-input :ref="'rename' + contactInformationType.id"
                            v-model="form.name"
                            :label="'Name'" :type="'text'"
                            :autofocus="true"
                            :input-class="'block w-full mb-3'"
                            :required="true"
                            :autocomplete="false"
                            :maxlength="255"
                            @esc-key-pressed="renameContactInformationTypeModalShownId = 0"
                />

                <text-input v-model="form.protocol"
                            :label="'Protocol'" :type="'text'"
                            :autofocus="true"
                            :input-class="'block w-full'"
                            :required="false"
                            :autocomplete="false"
                            :maxlength="255"
                            :help="'A contact information can be clickable. For instance, a phone number can be clickable and we will launch the default application in your computer associated with a phone number. If you do not know the protocol for the type you are adding, you can simply omit this field.'"
                            @esc-key-pressed="createContactInformationTypeModalShown = false"
                />
              </div>

              <div class="p-5 flex justify-between">
                <pretty-span :text="'Cancel'" :classes="'mr-3'" @click.prevent="renameContactInformationTypeModalShownId = 0" />
                <pretty-button :text="'Rename'" :state="loadingState" :icon="'check'" :classes="'save'" />
              </div>
            </form>
          </li>
        </ul>

        <!-- blank state -->
        <div v-if="localContactInformationTypes.length == 0" class="bg-white border border-gray-200 rounded-lg mb-6">
          <p class="p-5 text-center">Contact information types let you define how you can contact all your contacts (phone, email, …).</p>
        </div>
      </div>
    </main>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout';
import PrettyButton from '@/Shared/PrettyButton';
import PrettySpan from '@/Shared/PrettySpan';
import TextInput from '@/Shared/TextInput';
import Errors from '@/Shared/Errors';

export default {
  components: {
    Layout,
    PrettyButton,
    PrettySpan,
    TextInput,
    Errors,
  },

  props: {
    layoutData: {
      type: Object,
      default: null,
    },
    data: {
      type: Object,
      default: null,
    },
  },

  data() {
    return {
      loadingState: '',
      createContactInformationTypeModalShown: false,
      renameContactInformationTypeModalShownId: 0,
      localContactInformationTypes: [],
      form: {
        name: '',
        protocol: '',
        errors: [],
      },
    };
  },

  mounted() {
    this.localContactInformationTypes = this.data.contact_information_types;
  },

  methods: {
    showContactInformationTypeModal() {
      this.form.name = '';
      this.form.protocol = '';
      this.createContactInformationTypeModalShown = true;

      this.$nextTick(() => {
        this.$refs.newContactInformationType.focus();
      });
    },

    updateAdressTypeModal(contactInformationType) {
      this.form.name = contactInformationType.name;
      this.form.protocol = contactInformationType.protocol;
      this.renameContactInformationTypeModalShownId = contactInformationType.id;

      this.$nextTick(() => {
        this.$refs[`rename${contactInformationType.id}`].focus();
      });
    },

    submit() {
      this.loadingState = 'loading';

      axios.post(this.data.url.contact_information_type_store, this.form)
        .then(response => {
          this.flash('The contact information type has been created', 'success');
          this.localContactInformationTypes.unshift(response.data.data);
          this.loadingState = null;
          this.createContactInformationTypeModalShown = false;
        })
        .catch(error => {
          this.loadingState = null;
          this.form.errors = error.response.data;
        });
    },

    update(contactInformationType) {
      this.loadingState = 'loading';

      axios.put(contactInformationType.url.update, this.form)
        .then(response => {
          this.flash('The contact information type has been updated', 'success');
          this.localContactInformationTypes[this.localContactInformationTypes.findIndex(x => x.id === contactInformationType.id)] = response.data.data;
          this.loadingState = null;
          this.renameContactInformationTypeModalShownId = 0;
        })
        .catch(error => {
          this.loadingState = null;
          this.form.errors = error.response.data;
        });
    },

    destroy(contactInformationType) {
      if(confirm('Are you sure? This will remove the contact information types from all contacts, but won\'t delete the contacts themselves.')) {

        axios.delete(contactInformationType.url.destroy)
          .then(response => {
            this.flash('The contact information type has been deleted', 'success');
            var id = this.localContactInformationTypes.findIndex(x => x.id === contactInformationType.id);
            this.localContactInformationTypes.splice(id, 1);
          })
          .catch(error => {
            this.loadingState = null;
            this.form.errors = error.response.data;
          });
      }
    },
  },
};
</script>