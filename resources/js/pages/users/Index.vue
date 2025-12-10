<template>
    <AppLayout>
        <div class="mx-auto flex h-full w-full flex-1 flex-col p-4 space-y-3">
            
            <div class="flex items-center justify-between"> 
                <div> 
                    <h1 class="text-2xl font-semibold tracking-tight">Benutzerverwaltung</h1> 
                    <p class="text-muted-foreground mt-1 text-sm"> Verwalten Sie alle Benutzer Ihres Systems </p> 
                </div> 
                <div class="flex items-center gap-2">
                    <InviteButton/>
                    <CreateButton/>
                </div> 
            </div> 

            <div class="space-y-1">
                <div class="flex flex-row gap-2">
                    <Input type="text" placeholder="Benutzer suchen..." v-model="searchQuery" class="max-w-sm"/>
                    <Popover>
                        <PopoverTrigger as-child>
                            <Button variant="outline">
                                <CirclePlus class="h-4 w-4" />Status
                            </Button>
                        </PopoverTrigger>
                        <PopoverContent class="w-80">
                            <div class="grid gap-4">
                                <div class="space-y-2">
                                    <h4 class="font-medium leading-none">Status</h4>
                                </div>
                                <div class="grid gap-2">
                                    <div class="flex items-center gap-3">
                                        <Checkbox  id="verified" v-model="statusFilters.verified" @update:model-value="performSearch()"/>
                                        <Label for="verified">Verifiziert</Label>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <Checkbox id="not_verified" v-model="statusFilters.not_verified" @update:model-value="performSearch()"/>
                                        <Label for="not_verified">Nicht Verifiziert</Label>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <Checkbox id="two_fa_active" v-model="statusFilters.two_fa_active" @update:model-value="performSearch()"/>
                                        <Label for="two_fa_active">2FA aktiv</Label>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <Checkbox id="two_fa_not_active" v-model="statusFilters.two_fa_not_active" @update:model-value="performSearch()"/>
                                        <Label for="two_fa_not_active">2FA nicht aktiv</Label>
                                    </div>
                                </div>
                            </div>
                        </PopoverContent>
                    </Popover>
                    <div v-if="activeFilterCount > 0" class="flex flex-wrap items-center gap-2">      
                        <Badge v-if="statusFilters.verified" variant="secondary" class="gap-1 px-2 py-1 hover:bg-secondary/80">
                            <span>Verifiziert</span>
                            <button @click="removeFilter('verified')" class="ml-1 rounded-full p-0.5 hover:bg-muted">
                                <X class="h-3 w-3" />
                            </button>
                        </Badge>
                        <Badge v-if="statusFilters.not_verified" variant="secondary" class="gap-1 px-2 py-1 hover:bg-secondary/80">
                            <span>Nicht Verifiziert</span>
                            <button @click="removeFilter('not_verified')" class="ml-1 rounded-full p-0.5 hover:bg-muted">
                                <X class="h-3 w-3" />
                            </button>
                        </Badge>
                        <Badge v-if="statusFilters.two_fa_active" variant="secondary" class="gap-1 px-2 py-1 hover:bg-secondary/80">
                            <span>2FA aktiv</span>
                            <button @click="removeFilter('two_fa_active')" class="ml-1 rounded-full p-0.5 hover:bg-muted">
                                <X class="h-3 w-3" />
                            </button>
                        </Badge>
                        <Badge v-if="statusFilters.two_fa_not_active" variant="secondary" class="gap-1 px-2 py-1 hover:bg-secondary/80">
                            <span>2FA nicht aktiv</span>
                            <button @click="removeFilter('two_fa_not_active')" class="ml-1 rounded-full p-0.5 hover:bg-muted">
                                <X class="h-3 w-3" />
                            </button>
                        </Badge>
                    </div>
                    <Button v-if="hasActiveFilters" @click="clearSearch" variant="ghost">
                        <X class="h-3 w-3" />Zurücksetzen
                    </Button>
                </div>
                <span class="text-sm text-gray-400">{{ users.length }} Benutzer gefunden</span>
            </div>

            <div class="overflow-auto border border-gray-200 dark:border-zinc-800 rounded-xl shadow-sm">

                <div v-if="users.length > 0" class="divide-y divide-gray-200 dark:divide-zinc-800">

                    
                    <div v-for="user in users" :key="user.id" class="cursor-pointer px-2 py-3 transition-colors hover:bg-gray-50 dark:hover:bg-zinc-900 active:bg-gray-100">

                        <div class="flex justify-between">

                            <div class="flex">

                                <div class="flex items-center p-2">
                                    <AvatarWithOneChar :name="user.name" />
                                </div>

                                <div class="flex flex-col pl-4 gap-1">

                                    <div class="flex">
                                        <h3 class="truncate font-medium group-hover:text-primary">
                                            {{ user.name }}
                                        </h3>
                                    </div>

                                    <div class="flex items-center">
                                        <Mail class="mr-2 h-3.5 w-3.5 flex-shrink-0 text-gray-400" />
                                        <span class="truncate text-sm text-gray-600">{{ user.email }}</span>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                        <template v-if="user.roles && user.roles.length > 0">
                                            <Badge 
                                                v-for="role in user.roles" 
                                                :key="role.id"
                                                :variant="getRoleBadgeVariant(role.name)"
                                                class="px-2 py-0.5 text-xs font-medium flex items-center gap-1"
                                            >
                                                <UserCog class="h-3 w-3" />
                                                {{ role.name }}
                                            </Badge>
                                        </template>
                                        <span v-else class="text-xs text-gray-500 dark:text-gray-400 italic">
                                            Keine Rolle zugewiesen
                                        </span>
                                    </div>

                                    <div class="flex items-center text-xs text-gray-500 space-x-4">
                                        <div class="flex items-center gap-1">
                                            <Shield v-if="user.email_verified_at" class="h-3 w-3 text-emerald-500" />
                                            <ShieldOff v-else class="h-3 w-3 text-amber-500" />
                                            <span>{{ user.email_verified_at ? 'Verifiziert' : 'Nicht verifiziert' }}</span>
                                        </div>
                                        <div v-if="user.two_factor_confirmed_at" class="flex items-center gap-1">
                                            <KeyRound class="h-3 w-3 text-green-500" />
                                            <span>2FA aktiv</span>
                                        </div>
                                        <div v-if="!user.two_factor_confirmed_at" class="flex items-center gap-1">
                                            <KeyRound class="h-3 w-3 text-amber-500" />
                                            <span>2FA nicht aktiv</span>
                                        </div>
                                         <div v-if="user.permission_count" class="flex items-center gap-1">
                                            <Shield class="h-3 w-3 text-blue-500" />
                                            <span>{{ user.permission_count }} Berechtigungen</span>
                                        </div>
                                    </div>
                                    <div v-if="hasAdminRole(user.roles)" class="mt-1">
                                        <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 text-xs">
                                            <AlertCircle class="h-3 w-3" />
                                            <span>Vollzugriff auf System</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <ChevronRight class="h-4 w-4 flex-shrink-0 text-gray-400" />
                            </div>

                        </div>
                    </div>
                    
                </div>

                <div v-else class="flex h-full items-center justify-center py-12">
                    <div class="text-center">
                        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100">
                            <Users class="h-6 w-6 text-gray-400" />
                        </div>
                        <h3 class="text-lg font-medium">Keine Benutzer gefunden</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Es wurden keine Benutzer gefunden. 
                            <span v-if="hasActiveFilters">Passen Sie Ihre Suchkriterien an und versuchen Sie es erneut.</span>
                            <span v-else>Erstellen Sie einen neuen Benutzer oder laden Sie einen ein, um zu beginnen.</span>
                        </p>
                        <div class="space-x-2 mt-2" v-if="!hasActiveFilters">
                            <InviteButton/>
                            <CreateButton/>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </AppLayout>
</template>

<script setup lang="ts"> 
import AppLayout from '@/layouts/AppLayout.vue'; 
import { Button } from '@/components/ui/button'; 
import { Input } from '@/components/ui/input'; 
import { Mail, MailPlus, ChevronRight, CirclePlus, Shield, ShieldOff, KeyRound, User, UserCog, UserPlus, Users, X} from 'lucide-vue-next'; 
import { ref, computed, watch } from 'vue'; 
import { router } from '@inertiajs/vue3'; 

import { Label } from '@/components/ui/label'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover';
import { Checkbox } from '@/components/ui/checkbox'
import { Badge } from '@/components/ui/badge'
import CreateButton from '@/components/users/CreateButton.vue';
import InviteButton from '@/components/users/InviteButton.vue';
import AvatarWithOneChar from '@/components/users/AvatarWithOneChar.vue';

interface User { 
    id: number; 
    name: string; 
    email: string; 
    email_verified_at: string | null; 
    created_at: string;
    updated_at: string | null;
    two_factor_confirmed_at: string | null;
    roles: { 
        id: number; 
        name: string; 
    }[];
    permission_count?: number;
} 

interface Props { 
    users: User[]; 
    filters: { 
        search?: string;
        status?: StatusFilters;
    };
} 

interface UserRole {
    id: number;
    name: string;
    color?: string;
}


const getRoleBadgeVariant = (roleName: string) => {
    const variants: Record<string, string> = {
        'Administrator': 'destructive',
        'Admin': 'destructive',
        'Super Admin': 'destructive',
        'Manager': 'default',
        'Editor': 'outline',
        'Benutzer': 'secondary',
        'User': 'secondary',
        'Gast': 'outline',
        'Guest': 'outline',
    };
    
    return variants[roleName] || 'outline';
};

// Prüfen ob Admin-Rolle vorhanden
const hasAdminRole = (roles: UserRole[]) => {
    const adminRoles = ['Administrator', 'Admin', 'Super Admin'];
    return roles.some(role => adminRoles.includes(role.name));
};

const props = defineProps<Props>(); 

// start filter status logic

interface StatusFilters {
  verified: boolean;
  not_verified: boolean;
  two_fa_active: boolean;
  two_fa_not_active: boolean;
}

const statusFilters = ref<StatusFilters>({
  verified: props.filters.status?.verified === true || false,
  not_verified: props.filters.status?.not_verified === true || false,
  two_fa_active: props.filters.status?.two_fa_active === true || false,
  two_fa_not_active: props.filters.status?.two_fa_not_active === true || false,
});

// Berechnete Eigenschaften
const activeFilterCount = computed(() => {
  return Object.values(statusFilters.value).filter(Boolean).length;
});

const removeFilter = (filter: keyof StatusFilters) => {
  statusFilters.value[filter] = false;
  performSearch();
};

const searchQuery = ref(props.filters.search || '')

let searchTimeout: 5000 | ReturnType<typeof setTimeout>

watch([searchQuery], () => {
    performSearch()
})

const performSearch = () => {
    clearTimeout(searchTimeout);
    
    searchTimeout = setTimeout(() => {
        // URLSearchParams für saubere URL-Encoding
        const params = new URLSearchParams();
        
        // Search Parameter
        if (searchQuery.value) {
            params.append('search', searchQuery.value);
        }
        
        // Alle Status Filter als separate Parameter
        params.append('verified', statusFilters.value.verified ? '1' : '0');
        params.append('not_verified', statusFilters.value.not_verified ? '1' : '0');
        params.append('two_fa_active', statusFilters.value.two_fa_active ? '1' : '0');
        params.append('two_fa_not_active', statusFilters.value.two_fa_not_active ? '1' : '0');
        
        // URL zusammenbauen
        const queryString = params.toString();
        const url = `/admin/users${queryString ? '?' + queryString : ''}`;
        
        router.get(url, {}, {
            preserveState: true,
            replace: true
        });
    }, 300);
};
const clearSearch = () => {
    searchQuery.value = ''
    statusFilters.value = {
        verified: false,
        not_verified: false,
        two_fa_active: false,
        two_fa_not_active: false,
    };
    router.get('/admin/users', {}, {
        preserveState: true,
        replace: true
    })
}

const hasActiveFilters = computed(() => {
  const hasSearch = !!props.filters.search?.trim();
  const hasStatusFilters = activeFilterCount.value > 0;  
  return hasSearch || hasStatusFilters;
});

</script>
