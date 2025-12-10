<template>
     <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-primary/10">
        <span class="text-sm font-medium text-primary">
            {{ name.charAt(0).toUpperCase() }}
        </span>
    </div>
</template>

<script setup lang="ts">
interface Props { 
    name: string; 
} 
defineProps<Props>(); 
</script>