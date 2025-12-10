<template>
    <Button as="a" href="/admin/users/create" variant="outline">
        <UserPlus class="h-4 w-4" />Benutzer erstellen 
    </Button>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { UserPlus } from 'lucide-vue-next';
</script>
