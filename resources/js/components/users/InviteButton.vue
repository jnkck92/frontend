<template>
    <Button as="a" href="/admin/users/invite" variant="outline">
        <MailPlus class="h-4 w-4" />Einladen 
    </Button>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { MailPlus } from 'lucide-vue-next';
</script>
