<style lang="scss" scoped>
.item-list {
  &:hover:first-child {
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
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
    <nav class="bg-white sm:border-b">
      <div class="max-w-8xl mx-auto hidden px-4 py-2 sm:px-6 md:block">
        <div class="flex items-baseline justify-between space-x-6">
          <ul class="text-sm">
            <li class="mr-2 inline text-gray-600">You are here:</li>
            <li class="mr-2 inline">
              <inertia-link :href="data.url.settings" class="text-blue-500 hover:underline">Settings</inertia-link>
            </li>
            <li class="relative mr-2 inline">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="icon-breadcrumb relative inline h-3 w-3"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </li>
            <li class="mr-2 inline">
              <inertia-link :href="data.url.personalize" class="text-blue-500 hover:underline"
                >Personalize your account</inertia-link
              >
            </li>
            <li class="relative mr-2 inline">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="icon-breadcrumb relative inline h-3 w-3"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </li>
            <li class="inline">Group types</li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="relative sm:mt-20">
      <div class="mx-auto max-w-3xl px-2 py-2 sm:py-6 sm:px-6 lg:px-8">
        <!-- title + cta -->
        <div class="mb-6 mt-8 items-center justify-between sm:mt-0 sm:flex">
          <h3 class="mb-4 sm:mb-0"><span class="mr-1"> 👥 </span> All the group types</h3>
          <pretty-button
            v-if="!createGroupTypeModalShown"
            :text="'Add a group type'"
            :icon="'plus'"
            @click="showCreateGroupTypeModal" />
        </div>

        <!-- help text -->
        <div class="mb-6 flex rounded border bg-slate-50 px-3 py-2 text-sm">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 grow pr-2"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>

          <div>
            <p>
              A group is two or more people together. It can be a family, a household, a sport club. Whatever is
              important to you.
            </p>
          </div>
        </div>

        <!-- modal to create a group type -->
        <form
          v-if="createGroupTypeModalShown"
          class="mb-6 rounded-lg border border-gray-200 bg-white"
          @submit.prevent="submit()">
          <div class="border-b border-gray-200 p-5">
            <errors :errors="form.errors" />

            <text-input
              :ref="'newGroupType'"
              v-model="form.label"
              :label="'Name'"
              :type="'text'"
              :autofocus="true"
              :input-class="'block w-full'"
              :required="true"
              :autocomplete="false"
              :maxlength="255"
              @esc-key-pressed="createGroupTypeModalShown = false" />
          </div>

          <div class="flex justify-between p-5">
            <pretty-span :text="'Cancel'" :classes="'mr-3'" @click="createGroupTypeModalShown = false" />
            <pretty-button :text="'Save'" :state="loadingState" :icon="'plus'" :classes="'save'" />
          </div>
        </form>

        <!-- list of group types -->
        <div v-if="localGroupTypes.length > 0" class="mb-6">
          <draggable
            :list="localGroupTypes"
            item-key="id"
            :component-data="{ name: 'fade' }"
            handle=".handle"
            @change="updatePosition">
            <template #item="{ element }">
              <div v-if="editGroupTypeId != element.id" class="">
                <div class="item-list mb-2 rounded-lg border border-gray-200 bg-white py-2 pl-4 pr-5 hover:bg-slate-50">
                  <div class="mb-3 flex items-center justify-between">
                    <!-- icon to move position -->
                    <div class="mr-2 flex">
                      <svg
                        class="handle mr-2 cursor-move"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M7 7H9V9H7V7Z" fill="currentColor" />
                        <path d="M11 7H13V9H11V7Z" fill="currentColor" />
                        <path d="M17 7H15V9H17V7Z" fill="currentColor" />
                        <path d="M7 11H9V13H7V11Z" fill="currentColor" />
                        <path d="M13 11H11V13H13V11Z" fill="currentColor" />
                        <path d="M15 11H17V13H15V11Z" fill="currentColor" />
                        <path d="M9 15H7V17H9V15Z" fill="currentColor" />
                        <path d="M11 15H13V17H11V15Z" fill="currentColor" />
                        <path d="M17 15H15V17H17V15Z" fill="currentColor" />
                      </svg>

                      <span>{{ element.label }}</span>
                    </div>

                    <!-- actions -->
                    <ul class="text-sm">
                      <li
                        class="inline cursor-pointer text-blue-500 hover:underline"
                        @click="renameGroupTypeModal(element)">
                        Rename
                      </li>
                      <li class="ml-4 inline cursor-pointer text-red-500 hover:text-red-900" @click="destroy(element)">
                        Delete
                      </li>
                    </ul>
                  </div>

                  <!-- available roles -->
                  <div class="ml-8">
                    <p class="mb-1 text-sm text-gray-500">Roles:</p>

                    <draggable
                      :list="element.group_type_roles"
                      item-key="id"
                      :component-data="{ name: 'fade' }"
                      handle=".handle"
                      @change="updatePosition">
                      <template #item="{ element, id }">
                        <div v-if="editRoleId != element.id" class="">
                          <div
                            class="item-list mb-2 rounded-lg border border-gray-200 bg-white py-2 pl-4 pr-5 hover:bg-slate-50">
                            <div class="flex items-center justify-between">
                              <!-- icon to move position -->
                              <div class="mr-2 flex">
                                <svg
                                  class="handle mr-2 cursor-move"
                                  width="24"
                                  height="24"
                                  viewBox="0 0 24 24"
                                  fill="none"
                                  xmlns="http://www.w3.org/2000/svg">
                                  <path d="M7 7H9V9H7V7Z" fill="currentColor" />
                                  <path d="M11 7H13V9H11V7Z" fill="currentColor" />
                                  <path d="M17 7H15V9H17V7Z" fill="currentColor" />
                                  <path d="M7 11H9V13H7V11Z" fill="currentColor" />
                                  <path d="M13 11H11V13H13V11Z" fill="currentColor" />
                                  <path d="M15 11H17V13H15V11Z" fill="currentColor" />
                                  <path d="M9 15H7V17H9V15Z" fill="currentColor" />
                                  <path d="M11 15H13V17H11V15Z" fill="currentColor" />
                                  <path d="M17 15H15V17H17V15Z" fill="currentColor" />
                                </svg>

                                <span>{{ element.label }}</span>
                              </div>

                              <!-- actions -->
                              <ul class="text-sm">
                                <li
                                  class="inline cursor-pointer text-blue-500 hover:underline"
                                  @click="renameRoleModal(id, element)">
                                  Rename
                                </li>
                                <li
                                  class="ml-4 inline cursor-pointer text-red-500 hover:text-red-900"
                                  @click="destroyRole(element)">
                                  Delete
                                </li>
                              </ul>
                            </div>
                          </div>
                        </div>

                        <!-- edit a role form -->
                        <form
                          v-else
                          class="mb-6 rounded-lg border border-gray-200 bg-white"
                          @submit.prevent="updateRole(element)">
                          <div class="border-b border-gray-200 p-5">
                            <errors :errors="form.errors" />

                            <text-input
                              :ref="'newRole'"
                              v-model="form.label"
                              :label="'Name'"
                              :type="'text'"
                              :autofocus="true"
                              :input-class="'block w-full'"
                              :required="true"
                              :autocomplete="false"
                              :maxlength="255"
                              @esc-key-pressed="roleGroupTypeId = 0" />
                          </div>

                          <div class="flex justify-between p-5">
                            <pretty-span :text="'Cancel'" :classes="'mr-3'" @click="roleGroupTypeId = 0" />
                            <pretty-button :text="'Rename'" :state="loadingState" :icon="'check'" :classes="'save'" />
                          </div>
                        </form>
                      </template>
                    </draggable>

                    <!-- add a new role -->
                    <span
                      @click="showCreateRoleModal(element)"
                      v-if="
                        element.group_type_roles.length != 0 && !createRoleModalShown && roleGroupTypeId != element.id
                      "
                      class="inline cursor-pointer text-sm text-blue-500 hover:underline"
                      >Add a new role</span
                    >

                    <!-- form: create new role -->
                    <form
                      v-if="createRoleModalShown && roleGroupTypeId == element.id"
                      class="mb-6 rounded-lg border border-gray-200 bg-white"
                      @submit.prevent="submitRole(element)">
                      <div class="border-b border-gray-200 p-5">
                        <errors :errors="form.errors" />

                        <text-input
                          :ref="'newRole'"
                          v-model="form.label"
                          :label="'Name'"
                          :type="'text'"
                          :autofocus="true"
                          :input-class="'block w-full'"
                          :required="true"
                          :autocomplete="false"
                          :maxlength="255"
                          @esc-key-pressed="createRoleModalShown = false" />
                      </div>

                      <div class="flex justify-between p-5">
                        <pretty-span :text="'Cancel'" :classes="'mr-3'" @click="createRoleModalShown = false" />
                        <pretty-button :text="'Save'" :state="loadingState" :icon="'plus'" :classes="'save'" />
                      </div>
                    </form>

                    <!-- blank state -->
                    <div
                      v-if="
                        element.group_type_roles.length == 0 && !createRoleModalShown && roleGroupTypeId != element.id
                      "
                      class="mb-6 rounded-lg border border-gray-200 bg-white">
                      <p class="p-5 text-center">
                        No roles yet.
                        <span
                          @click="showCreateRoleModal(element)"
                          class="block cursor-pointer text-sm text-blue-500 hover:underline"
                          >Add a new role</span
                        >
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <form
                v-else
                class="item-list border-b border-gray-200 hover:bg-slate-50"
                @submit.prevent="update(element)">
                <div class="border-b border-gray-200 p-5">
                  <errors :errors="form.errors" />

                  <text-input
                    :ref="'rename' + element.id"
                    v-model="form.label"
                    :label="'Name'"
                    :type="'text'"
                    :autofocus="true"
                    :input-class="'block w-full'"
                    :required="true"
                    :autocomplete="false"
                    :maxlength="255"
                    @esc-key-pressed="editGroupTypeId = 0" />
                </div>

                <div class="flex justify-between p-5">
                  <pretty-span :text="'Cancel'" :classes="'mr-3'" @click.prevent="editGroupTypeId = 0" />
                  <pretty-button :text="'Rename'" :state="loadingState" :icon="'check'" :classes="'save'" />
                </div>
              </form>
            </template>
          </draggable>
        </div>

        <!-- blank state -->
        <div v-if="localGroupTypes.length == 0" class="mb-6 rounded-lg border border-gray-200 bg-white">
          <p class="p-5 text-center">Group types let you group people together.</p>
        </div>
      </div>
    </main>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout';
import PrettyButton from '@/Shared/Form/PrettyButton';
import PrettySpan from '@/Shared/Form/PrettySpan';
import TextInput from '@/Shared/Form/TextInput';
import Errors from '@/Shared/Form/Errors';
import draggable from 'vuedraggable';

export default {
  components: {
    Layout,
    PrettyButton,
    PrettySpan,
    TextInput,
    Errors,
    draggable,
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
      createGroupTypeModalShown: false,
      createRoleModalShown: false,
      roleGroupTypeId: 0,
      editGroupTypeId: 0,
      editRoleId: 0,
      localGroupTypes: [],
      form: {
        label: '',
        position: '',
        errors: [],
      },
    };
  },

  mounted() {
    this.localGroupTypes = this.data.group_types;
  },

  methods: {
    showCreateGroupTypeModal() {
      this.form.label = '';
      this.form.position = '';
      this.createGroupTypeModalShown = true;

      this.$nextTick(() => {
        this.$refs.newGroupType.focus();
      });
    },

    showCreateRoleModal(groupType) {
      this.form.label = '';
      this.form.position = '';
      this.createRoleModalShown = true;
      this.roleGroupTypeId = groupType.id;

      this.$nextTick(() => {
        this.$refs.newRole.focus();
      });
    },

    renameGroupTypeModal(groupType) {
      this.form.label = groupType.label;
      this.editGroupTypeId = groupType.id;
    },

    renameRoleModal(groupType, role) {
      this.form.label = role.label;
      this.editGroupTypeId = groupType;
      this.editRoleId = role.id;
    },

    submit() {
      this.loadingState = 'loading';

      axios
        .post(this.data.url.store, this.form)
        .then((response) => {
          this.flash('The group type has been created', 'success');
          this.localGroupTypes.push(response.data.data);
          this.loadingState = null;
          this.createGroupTypeModalShown = false;
        })
        .catch((error) => {
          this.loadingState = null;
          this.form.errors = error.response.data;
        });
    },

    update(groupType) {
      this.loadingState = 'loading';

      axios
        .put(groupType.url.update, this.form)
        .then((response) => {
          this.flash('The group type has been updated', 'success');
          this.localGroupTypes[this.localGroupTypes.findIndex((x) => x.id === groupType.id)] = response.data.data;
          this.loadingState = null;
          this.editGroupTypeId = 0;
        })
        .catch((error) => {
          this.loadingState = null;
          this.form.errors = error.response.data;
        });
    },

    destroy(groupType) {
      if (confirm('Are you sure? This can not be undone.')) {
        axios
          .delete(groupType.url.destroy)
          .then((response) => {
            this.flash('The group type has been deleted', 'success');
            var id = this.localGroupTypes.findIndex((x) => x.id === groupType.id);
            this.localGroupTypes.splice(id, 1);
          })
          .catch((error) => {
            this.loadingState = null;
            this.form.errors = error.response.data;
          });
      }
    },

    updatePosition(event) {
      // the event object comes from the draggable component
      this.form.position = event.moved.newIndex + 1;

      axios
        .post(event.moved.element.url.position, this.form)
        .then((response) => {
          this.flash('The order has been saved', 'success');
        })
        .catch((error) => {
          this.loadingState = null;
          this.errors = error.response.data;
        });
    },

    submitRole(groupType) {
      this.loadingState = 'loading';

      axios
        .post(groupType.url.store, this.form)
        .then((response) => {
          this.flash('The role has been created', 'success');
          var id = this.localGroupTypes.findIndex((x) => x.id === groupType.id);
          this.localGroupTypes[id].group_type_roles.push(response.data.data);
          this.loadingState = null;
          this.roleGroupTypeId = 0;
          this.createRoleModalShown = false;
        })
        .catch((error) => {
          this.loadingState = null;
          this.form.errors = error.response.data;
        });
    },

    updateRole(role) {
      this.loadingState = 'loading';

      axios
        .put(role.url.update, this.form)
        .then((response) => {
          this.flash('The role has been updated', 'success');

          var groupTypeId = this.localGroupTypes.findIndex((x) => x.id === role.group_type_id);
          var roleId = this.localGroupTypes[groupTypeId].group_type_roles.findIndex((x) => x.id === role.id);
          this.localGroupTypes[groupTypeId].group_type_roles[roleId] = response.data.data;

          this.loadingState = null;
          this.roleGroupTypeId = 0;
          this.editRoleId = 0;
        })
        .catch((error) => {
          this.loadingState = null;
          this.form.errors = error.response.data;
        });
    },

    destroyRole(role) {
      if (confirm('Are you sure? This can not be undone.')) {
        axios
          .delete(role.url.destroy)
          .then((response) => {
            this.flash('The role has been deleted', 'success');

            var groupTypeId = this.localGroupTypes.findIndex((x) => x.id === role.group_type_id);
            var roleId = this.localGroupTypes[groupTypeId].group_type_roles.findIndex((x) => x.id === role.id);
            this.localGroupTypes[groupTypeId].group_type_roles.splice(roleId, 1);
          })
          .catch((error) => {
            this.loadingState = null;
            this.form.errors = error.response.data;
          });
      }
    },
  },
};
</script>