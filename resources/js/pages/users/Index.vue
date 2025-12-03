<template>
    <AppLayout>
        <div class="mx-auto flex h-full w-full max-w-6xl flex-1 flex-col p-4">
            <Link href="/admin/users/create" class="mb-4">
                <Button>Create</Button>
            </Link>
            <div class="space-y-2">
                <Item variant="outline" v-for="user in users" :key="user.id" as-child>
                    <Link :href="`/users/${user.id}`">
                        <ItemContent>
                            <ItemTitle>{{ user.name }}</ItemTitle>
                            <Badge variant="secondary" class="text-xs">
                                {{ user.email }}
                            </Badge>
                        </ItemContent>
                        <ItemActions>
                            <ChevronRightIcon class="size-4" />
                        </ItemActions>
                    </Link>
                </Item>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';

import { Link } from '@inertiajs/vue3';
import { ChevronRightIcon } from 'lucide-vue-next'
import { Item, ItemActions, ItemContent, ItemTitle } from '@/components/ui/item'
import { Button } from '@/components/ui/button'

interface Props {
    users: Array<any>;
}

defineProps<Props>();

</script>