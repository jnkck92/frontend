<template>
    <AppLayout>
        
        <div className='mb-2 flex items-center justify-between space-y-2 flex-wrap'>
            <div>
              <h2 className='text-2xl font-bold tracking-tight'>User List</h2>
              <p className='text-muted-foreground'>
                Manage your users and their roles here.
              </p>
            </div>
        </div>


        <div class="flex mb-4 space-x-2">
            <Dialog>
                <DialogTrigger>
                    <Button variant="outline">
                        <span>Invite User</span> <UserPlus />
                    </Button>
                </DialogTrigger>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle className='flex items-center gap-2 mb-1'>
                            <MailPlus /> Invite User
                        </DialogTitle>
                        <DialogDescription class="text-left">
                            Invite new user to join your team by sending them an email
                            invitation. Assign a role to define their access level.
                        </DialogDescription>
                    </DialogHeader>
                        <form id="dialogForm" class="space-y-4">
                            <FormField name="email">
                                <FormItem>
                                    <FormLabel>Email</FormLabel>
                                    <FormControl>
                                        <Input type="email" placeholder="Email" />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>
                            
                            <FormField name="Role">
                                <FormItem>
                                    <FormLabel>Role</FormLabel>
                                    <FormControl>
                                        <Input type="text" placeholder="Role"  />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField name="description">
                                <FormItem>
                                    <FormLabel>Description (optional)</FormLabel>
                                    <FormControl>
                                        <Textarea type="text" placeholder="Add a personal note to your invitation (optional)" rows="4"/>
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>
                        </form>
                    <DialogFooter class="flex space-y-2">
                        <DialogClose asChild>
                            <Button variant='outline'>Cancel</Button>
                        </DialogClose>
                        <Button type='submit'>
                            Invite <Send />
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
            <Link href="/admin/users/create">
                <Button variant="outline">
                    <span>Add User</span> <UserPlus />
                </Button>
            </Link>
        </div>
        
         <div class="grid grid-cols-1 lg:grid-cols-3 gap-2 mb-6">
            <div class="p-3 rounded-lg border shadow-sm">
                <div class="`text-lg lg:text-2xl font-bold dark:text-gray-100`">{{ 1 }}</div>
                <div class="text-xs lg:text-sm text-gray-500 dark:text-neutral-500 truncate">Total Users</div>
            </div>
            <div class="p-3 rounded-lg border shadow-sm">
                <div class="`text-lg lg:text-2xl font-bold dark:text-gray-100`">{{ 4 }}</div>
                <div class="text-xs lg:text-sm text-gray-500 dark:text-neutral-500 truncate">Active Users</div>
            </div>
            <div class="p-3 rounded-lg border shadow-sm">
                <div class="`text-lg lg:text-2xl font-bold dark:text-gray-100`">{{ 1 }}</div>
                <div class="text-xs lg:text-sm text-gray-500 dark:text-neutral-500 truncate">Inactive Users</div>
            </div>
        </div>

        <div class="space-y-2">
            <Item variant="outline" v-for="user in users" :key="user.id" as-child class="shadow-sm">
                <Link :href="`/users/${user.id}`">
                    <ItemContent>
                        <ItemTitle>{{ user.name }}</ItemTitle>
                        <ItemDescription>{{ user.email }}</ItemDescription>
                    </ItemContent>
                    <ItemActions>
                        <ChevronRightIcon class="size-4" />
                    </ItemActions>
                </Link>
            </Item>
        </div>
        
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ChevronRightIcon } from 'lucide-vue-next'
import { Item, ItemActions, ItemContent, ItemDescription, ItemTitle } from '@/components/ui/item';
import { Button } from '@/components/ui/button';
import { MailPlus, UserPlus, Send } from 'lucide-vue-next';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'


import {
  FormControl,
  FormDescription,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form'

import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';

import {
  Field,
  FieldDescription,
  FieldGroup,
  FieldLabel,
  FieldSet,
} from '@/components/ui/field'

interface Props {
    users: Array<any>;
}

defineProps<Props>();

</script>