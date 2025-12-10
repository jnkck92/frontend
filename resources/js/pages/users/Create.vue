<template>
    <AppLayout>
        <div class="mx-auto flex h-full w-full flex-1 flex-col p-4 space-y-6">
            
            <!-- Header -->
            <div class="flex items-center justify-between"> 
                <div> 
                    <h1 class="text-2xl font-semibold tracking-tight">Neuen Benutzer erstellen</h1> 
                    <p class="text-muted-foreground mt-1 text-sm"> Erstellen Sie einen neuen Benutzer für Ihr System </p> 
                </div> 
            </div>

            <!-- Formular -->
            <div class="grid gap-6">
                <div class="rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm bg-white dark:bg-zinc-900 p-6">
                    <form @submit.prevent="submitForm" class="space-y-6">
                        
                        <!-- Persönliche Informationen -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-3 mb-2">
                                <User class="h-5 w-5 text-gray-500" />
                                <h3 class="text-lg font-medium">Persönliche Informationen</h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Name -->
                                <div class="space-y-2">
                                    <Label for="name" class="text-sm font-medium">
                                        Vollständiger Name *
                                    </Label>
                                    <Input 
                                        id="name" 
                                        v-model="form.name" 
                                        placeholder="Max Mustermann" 
                                        :class="{ 'border-red-500': formErrors.name }"
                                    />
                                    <p v-if="formErrors.name" class="text-sm text-red-500">{{ formErrors.name }}</p>
                                </div>

                                <!-- E-Mail -->
                                <div class="space-y-2">
                                    <Label for="email" class="text-sm font-medium">
                                        E-Mail-Adresse *
                                    </Label>
                                    <Input 
                                        id="email" 
                                        type="email" 
                                        v-model="form.email" 
                                        placeholder="max.mustermann@beispiel.de" 
                                        :class="{ 'border-red-500': formErrors.email }"
                                    />
                                    <p v-if="formErrors.email" class="text-sm text-red-500">{{ formErrors.email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Passwort -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-3 mb-2">
                                <KeyRound class="h-5 w-5 text-gray-500" />
                                <h3 class="text-lg font-medium">Passwort</h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Passwort -->
                                <div class="space-y-2">
                                    <Label for="password" class="text-sm font-medium">
                                        Passwort *
                                    </Label>
                                    <Input 
                                        id="password" 
                                        type="password" 
                                        v-model="form.password" 
                                        placeholder="********" 
                                        :class="{ 'border-red-500': formErrors.password }"
                                    />
                                    <p v-if="formErrors.password" class="text-sm text-red-500">{{ formErrors.password }}</p>
                                    <p class="text-xs text-gray-500">Mindestens 8 Zeichen</p>
                                </div>

                                <!-- Passwort Bestätigung -->
                                <div class="space-y-2">
                                    <Label for="password_confirmation" class="text-sm font-medium">
                                        Passwort bestätigen *
                                    </Label>
                                    <Input 
                                        id="password_confirmation" 
                                        type="password" 
                                        v-model="form.password_confirmation" 
                                        placeholder="********" 
                                        :class="{ 'border-red-500': formErrors.password_confirmation }"
                                    />
                                    <p v-if="formErrors.password_confirmation" class="text-sm text-red-500">{{ formErrors.password_confirmation }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Rollen und Berechtigungen -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-3 mb-2">
                                <UserCog class="h-5 w-5 text-gray-500" />
                                <h3 class="text-lg font-medium">Rollen und Berechtigungen</h3>
                            </div>
                            
                            <!-- Rollen Dropdown -->
                            <div class="space-y-2">
                                <Label for="role" class="text-sm font-medium">
                                    Rolle zuweisen *
                                </Label>
                                <Select v-model="form.role_id" :disabled="isLoadingRoles">
                                    <SelectTrigger id="role" :class="{ 'border-red-500': formErrors.role_id }">
                                        <SelectValue placeholder="Rolle auswählen" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectLabel>Verfügbare Rollen</SelectLabel>
                                            <SelectItem 
                                                v-for="role in availableRoles" 
                                                :key="role.id" 
                                                :value="role.id"
                                            >
                                                <div class="flex items-center gap-2">
                                                    <div class="flex-shrink-0">
                                                        <Shield class="h-4 w-4" :class="getRoleColor(role.name)" />
                                                    </div>
                                                    <div class="flex-1">
                                                        <span>{{ role.name }}</span>
                                                        <p class="text-xs text-gray-500">{{ role.description }}</p>
                                                    </div>
                                                </div>
                                            </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                                <p v-if="formErrors.role_id" class="text-sm text-red-500">{{ formErrors.role_id }}</p>
                                <p class="text-xs text-gray-500">
                                    Die Rolle definiert die Berechtigungen des Benutzers im System.
                                </p>
                            </div>

                            <!-- Rollen-Beschreibung -->
                            <div v-if="selectedRole" class="mt-4 p-3 rounded-lg border border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800/50">
                                <div class="flex items-start gap-3">
                                    <Info class="h-4 w-4 text-blue-500 mt-0.5 flex-shrink-0" />
                                    <div class="space-y-1">
                                        <p class="text-sm font-medium">{{ selectedRole.name }}</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ selectedRole.description }}</p>
                                        <div v-if="selectedRole.permissions && selectedRole.permissions.length > 0" class="pt-2">
                                            <p class="text-xs font-medium text-gray-700 dark:text-gray-300">Berechtigungen:</p>
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                <Badge 
                                                    v-for="permission in selectedRole.permissions.slice(0, 5)" 
                                                    :key="permission"
                                                    variant="outline" 
                                                    class="text-xs"
                                                >
                                                    {{ permission }}
                                                </Badge>
                                                <Badge 
                                                    v-if="selectedRole.permissions.length > 5" 
                                                    variant="outline" 
                                                    class="text-xs"
                                                >
                                                    +{{ selectedRole.permissions.length - 5 }} mehr
                                                </Badge>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- E-Mail Verifizierung -->
                            <div class="space-y-3 pt-4 border-t border-gray-200 dark:border-zinc-800">
                                <div class="flex items-center space-x-2">
                                    <Checkbox 
                                        id="email_verified" 
                                        v-model="form.email_verified"
                                    />
                                    <Label for="email_verified" class="text-sm font-medium cursor-pointer">
                                        E-Mail-Adresse als verifiziert markieren
                                    </Label>
                                </div>
                                <p class="text-xs text-gray-500">
                                    Der Benutzer muss seine E-Mail-Adresse nicht bestätigen, um sich anzumelden.
                                </p>
                            </div>
                        </div>

                        <!-- Benachrichtigungen -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-3 mb-2">
                                <Mail class="h-5 w-5 text-gray-500" />
                                <h3 class="text-lg font-medium">Benachrichtigungen</h3>
                            </div>
                            
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2">
                                    <Checkbox 
                                        id="send_welcome_email" 
                                        v-model="form.send_welcome_email"
                                    />
                                    <Label for="send_welcome_email" class="text-sm font-medium cursor-pointer">
                                        Willkommens-E-Mail senden
                                    </Label>
                                </div>
                                <p class="text-xs text-gray-500">
                                    Sendet eine Willkommens-E-Mail mit Anmeldeinformationen an den neuen Benutzer.
                                </p>
                            </div>
                        </div>

                        <!-- Formular-Aktionen -->
                        <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-zinc-800">
                            <Button 
                                type="button" 
                                variant="outline" 
                                @click="goBack"
                            >
                                Zurück
                            </Button>
                            
                            <div class="flex items-center gap-3">
                                <Button 
                                    type="button" 
                                    variant="outline" 
                                    @click="resetForm"
                                >
                                    Zurücksetzen
                                </Button>
                                
                                <Button 
                                    type="submit" 
                                    :disabled="isSubmitting || isLoadingRoles"
                                    class="min-w-[120px]"
                                >
                                    <template v-if="isSubmitting">
                                        <LoaderCircle class="h-4 w-4 mr-2 animate-spin" />
                                        Wird erstellt...
                                    </template>
                                    <template v-else>
                                        <UserPlus class="h-4 w-4 mr-2" />
                                        Benutzer erstellen
                                    </template>
                                </Button>
                            </div>
                        </div>

                    </form>
                </div>

                <!-- Formular-Hilfe -->
                <div class="rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm bg-gray-50 dark:bg-zinc-900/50 p-6">
                    <div class="flex items-start gap-3">
                        <Info class="h-5 w-5 text-blue-500 mt-0.5" />
                        <div class="space-y-2">
                            <h4 class="font-medium">Informationen zum Benutzer erstellen</h4>
                            <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1 list-disc list-inside">
                                <li>Alle mit * markierten Felder sind Pflichtfelder</li>
                                <li>Das Passwort muss mindestens 8 Zeichen lang sein</li>
                                <li>Wählen Sie eine passende Rolle für die Berechtigungen des Benutzers</li>
                                <li>Verifizierte Benutzer können sofort auf das System zugreifen</li>
                                <li>Die Willkommens-E-Mail enthält Anmeldeinformationen</li>
                            </ul>
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
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { Badge } from '@/components/ui/badge';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { 
    ChevronLeft, 
    User, 
    KeyRound, 
    UserCog, 
    Mail, 
    UserPlus, 
    LoaderCircle,
    Info,
    Shield
} from 'lucide-vue-next'; 
import { ref, reactive, computed, onMounted } from 'vue'; 
import { router } from '@inertiajs/vue3';

interface Role {
    id: number;
    name: string;
    description: string;
    permissions?: string[];
    is_default?: boolean;
}

interface FormData {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
    role_id: number | null;
    email_verified: boolean;
    send_welcome_email: boolean;
}

interface FormErrors {
    name?: string;
    email?: string;
    password?: string;
    password_confirmation?: string;
    role_id?: string;
    [key: string]: string | undefined;
}

// Props für verfügbare Rollen
interface Props {
    availableRoles?: Role[];
}

const props = withDefaults(defineProps<Props>(), {
    availableRoles: () => []
});

// Formular-Daten
const form = reactive<FormData>({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role_id: null,
    email_verified: false,
    send_welcome_email: true
});

// Formular-Fehler
const formErrors = ref<FormErrors>({});

// Ladezustände
const isSubmitting = ref(false);
const isLoadingRoles = ref(false);

// Ausgewählte Rolle basierend auf role_id
const selectedRole = computed(() => {
    if (!form.role_id) return null;
    return props.availableRoles.find(role => role.id === form.role_id) || null;
});

// Farbe für Rollen-Icons basierend auf Rollennamen
const getRoleColor = (roleName: string) => {
    const colors: Record<string, string> = {
        'Administrator': 'text-red-500',
        'Admin': 'text-red-500',
        'Super Admin': 'text-purple-500',
        'Manager': 'text-blue-500',
        'Editor': 'text-green-500',
        'User': 'text-gray-500',
        'Benutzer': 'text-gray-500',
        'Gast': 'text-yellow-500',
        'Guest': 'text-yellow-500',
    };
    
    return colors[roleName] || 'text-gray-500';
};

// Standard-Rolle setzen (falls vorhanden)
onMounted(() => {
    if (props.availableRoles.length > 0) {
        const defaultRole = props.availableRoles.find(role => role.is_default);
        if (defaultRole) {
            form.role_id = defaultRole.id;
        }
    }
});

// Zurück zur Übersicht
const goBack = () => {
    router.get('/admin/users');
};

// Formular zurücksetzen
const resetForm = () => {
    form.name = '';
    form.email = '';
    form.password = '';
    form.password_confirmation = '';
    form.role_id = null;
    form.email_verified = false;
    form.send_welcome_email = true;
    formErrors.value = {};
    
    // Standard-Rolle wieder setzen
    if (props.availableRoles.length > 0) {
        const defaultRole = props.availableRoles.find(role => role.is_default);
        if (defaultRole) {
            form.role_id = defaultRole.id;
        }
    }
};

// Formular absenden
const submitForm = async () => {
    // Validierung vor dem Absenden
    if (!form.role_id) {
        formErrors.value.role_id = 'Bitte wählen Sie eine Rolle aus';
        return;
    }
    
    isSubmitting.value = true;
    formErrors.value = {};

    try {
        await router.post('/admin/users', form, {
            preserveScroll: true,
            onSuccess: () => {
                // Erfolgreich erstellt - weiterleiten zur Übersicht
                router.visit('/admin/users', {
                    only: ['users', 'filters'],
                    preserveScroll: true,
                    preserveState: true
                });
            },
            onError: (errors) => {
                // Fehler behandeln
                formErrors.value = errors as FormErrors;
            },
            onFinish: () => {
                isSubmitting.value = false;
            }
        });
    } catch (error) {
        console.error('Fehler beim Erstellen des Benutzers:', error);
        isSubmitting.value = false;
    }
};
</script>