<template>
    <AppLayout>
        <div class="mx-auto flex h-full w-full max-w-6xl flex-1 flex-col p-4">
            <div class="space-y-2">
                <Form v-bind="UserController.store.form()" reset-on-success v-slot="{ errors, processing }">
                    <FieldGroup>
                        <FieldSet>
                            <FieldGroup>
                                <Field>
                                    <FieldLabel for="name">
                                        Name
                                    </FieldLabel>
                                    <Input id="name" placeholder="Name" required name="name"/>
                                </Field>
                                <Field>
                                    <FieldLabel for="email">
                                        Email
                                    </FieldLabel>
                                    <Input id="email" placeholder="Email" required name="email"/>
                                </Field>
                                <Field>
                                    <FieldLabel for="password">
                                        Password
                                    </FieldLabel>
                                    <Input id="password" placeholder="Password" required name="password" type="password"/>
                                </Field>
                                <Field>
                                    <FieldLabel for="password_confirmation">
                                        Password Confirmation
                                    </FieldLabel>
                                    <Input id="password_confirmation" placeholder="Password Confirmation" required name="password_confirmation" type="password"/>
                                </Field>
                            </FieldGroup>
                        </FieldSet>
                        <Field orientation="horizontal">
                            <Button type="submit">
                                <Spinner class="animate-spin" v-if="processing"/>
                                Save
                            </Button>
                        </Field>
                    </FieldGroup>
                </Form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Form } from '@inertiajs/vue3';
import UserController from '@/actions/App/Http/Controllers/UserController';

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Field, FieldGroup, FieldLabel, FieldSet } from '@/components/ui/field'
import { Spinner } from '@/components/ui/spinner'

</script>