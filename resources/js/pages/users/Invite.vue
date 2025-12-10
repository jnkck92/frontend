<template>
    <AppLayout>
        <div class="mx-auto flex h-full w-full flex-1 flex-col p-4 space-y-6">
            
            <!-- Header -->
            <div class="flex items-center justify-between"> 
                <div> 
                    <h1 class="text-2xl font-semibold tracking-tight">Benutzer einladen</h1> 
                    <p class="text-muted-foreground mt-1 text-sm"> Laden Sie einen neuen Benutzer per E-Mail ein </p> 
                </div> 
            </div>

            <!-- Einladungsformular -->
            <div class="grid gap-6">
                <div class="rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm bg-white dark:bg-zinc-900 p-6">
                    <form @submit.prevent="submitInvitation" class="space-y-6">
                        
                        <!-- Einladungsdetails -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-3 mb-2">
                                <MailPlus class="h-5 w-5 text-gray-500" />
                                <h3 class="text-lg font-medium">Einladungsdetails</h3>
                            </div>
                            
                            <!-- E-Mail -->
                            <div class="space-y-2">
                                <Label for="email" class="text-sm font-medium">
                                    E-Mail-Adresse des neuen Benutzers *
                                    <span class="text-red-500 ml-1">*</span>
                                </Label>
                                <Input 
                                    id="email" 
                                    type="email" 
                                    v-model="form.email" 
                                    placeholder="max.mustermann@beispiel.de" 
                                    :class="{ 'border-red-500': formErrors.email }"
                                    autofocus
                                />
                                <p v-if="formErrors.email" class="text-sm text-red-500">{{ formErrors.email }}</p>
                                <p class="text-xs text-gray-500">Die Einladung wird an diese E-Mail-Adresse gesendet</p>
                            </div>

                            <!-- Rolle -->
                            <div class="space-y-3 pt-4 border-t border-gray-200 dark:border-zinc-800">
                                <Label class="text-sm font-medium">Rolle zuweisen</Label>
                                <div class="space-y-3">
                                    <div class="flex items-center space-x-2">
                                        <RadioGroup 
                                            v-model="form.role" 
                                            class="flex flex-col space-y-2"
                                        >
                                            <div class="flex items-center space-x-2">
                                                <RadioGroupItem id="user" value="user" />
                                                <Label for="user" class="text-sm font-medium cursor-pointer">
                                                    Standard-Benutzer
                                                </Label>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <RadioGroupItem id="admin" value="admin" />
                                                <Label for="admin" class="text-sm font-medium cursor-pointer">
                                                    Administrator
                                                </Label>
                                            </div>
                                        </RadioGroup>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500">
                                    Administratoren haben vollen Zugriff auf alle Systemfunktionen und können andere Benutzer verwalten.
                                </p>
                            </div>

                            <!-- Einladungsnachricht -->
                            <div class="space-y-2 pt-4 border-t border-gray-200 dark:border-zinc-800">
                                <Label for="message" class="text-sm font-medium">
                                    Persönliche Nachricht (optional)
                                </Label>
                                <textarea 
                                    id="message" 
                                    v-model="form.message" 
                                    placeholder="Fügen Sie eine persönliche Nachricht zur Einladung hinzu..." 
                                    rows="3"
                                    class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    :class="{ 'border-red-500': formErrors.message }"
                                ></textarea>
                                <p v-if="formErrors.message" class="text-sm text-red-500">{{ formErrors.message }}</p>
                                <p class="text-xs text-gray-500">Diese Nachricht wird in der Einladungs-E-Mail enthalten sein</p>
                            </div>
                        </div>

                        <!-- Berechtigungen -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-3 mb-2">
                                <UserCog class="h-5 w-5 text-gray-500" />
                                <h3 class="text-lg font-medium">Berechtigungen und Optionen</h3>
                            </div>
                            
                            <div class="space-y-4">
                                <!-- Zugriffseinschränkungen -->
                                <div class="space-y-3">
                                    <div class="flex items-center space-x-2">
                                        <Checkbox 
                                            id="restrict_access" 
                                            v-model="form.restrict_access"
                                        />
                                        <Label for="restrict_access" class="text-sm font-medium cursor-pointer">
                                            Zugriff auf bestimmte Bereiche beschränken
                                        </Label>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        Der Benutzer erhält nur Zugriff auf ausgewählte Module und Funktionen.
                                    </p>
                                </div>

                                <!-- Automatische Verifizierung -->
                                <div class="space-y-3 pt-4 border-t border-gray-200 dark:border-zinc-800">
                                    <div class="flex items-center space-x-2">
                                        <Checkbox 
                                            id="auto_verify" 
                                            v-model="form.auto_verify"
                                        />
                                        <Label for="auto_verify" class="text-sm font-medium cursor-pointer">
                                            E-Mail automatisch als verifiziert markieren
                                        </Label>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        Der Benutzer muss seine E-Mail-Adresse nicht bestätigen, um sich anzumelden.
                                    </p>
                                </div>

                                <!-- Einladungslink-Gültigkeit -->
                                <div v-if="!form.auto_verify" class="space-y-3 pt-4 border-t border-gray-200 dark:border-zinc-800">
                                    <Label for="expiry_days" class="text-sm font-medium">
                                        Einladungslink gültig für
                                    </Label>
                                    <div class="flex items-center space-x-3">
                                        <Input 
                                            id="expiry_days" 
                                            type="number" 
                                            v-model="form.expiry_days" 
                                            min="1"
                                            max="30"
                                            class="w-24"
                                            :class="{ 'border-red-500': formErrors.expiry_days }"
                                        />
                                        <span class="text-sm text-gray-600">Tage</span>
                                    </div>
                                    <p v-if="formErrors.expiry_days" class="text-sm text-red-500">{{ formErrors.expiry_days }}</p>
                                    <p class="text-xs text-gray-500">
                                        Der Einladungslink läuft nach dieser Anzahl von Tagen ab (Standard: 7 Tage)
                                    </p>
                                </div>
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
                                    :disabled="isSubmitting"
                                    class="min-w-[120px] bg-indigo-600 hover:bg-indigo-700"
                                >
                                    <template v-if="isSubmitting">
                                        <LoaderCircle class="h-4 w-4 mr-2 animate-spin" />
                                        Wird eingeladen...
                                    </template>
                                    <template v-else>
                                        <Send class="h-4 w-4 mr-2" />
                                        Einladung senden
                                    </template>
                                </Button>
                            </div>
                        </div>

                    </form>
                </div>

                <!-- Einladungsprozess-Info -->
                <div class="rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm bg-indigo-50 dark:bg-indigo-900/20 p-6">
                    <div class="flex items-start gap-3">
                        <Info class="h-5 w-5 text-indigo-500 mt-0.5" />
                        <div class="space-y-3">
                            <h4 class="font-medium">Wie funktioniert die Einladung?</h4>
                            <ol class="text-sm text-gray-600 dark:text-gray-400 space-y-2 list-decimal list-inside">
                                <li>Der eingeladene Benutzer erhält eine E-Mail mit einem Einladungslink</li>
                                <li>Nach dem Klick auf den Link wird er zur Registrierungsseite weitergeleitet</li>
                                <li>Er kann dort seinen Namen und ein Passwort festlegen</li>
                                <li>Nach erfolgreicher Registrierung hat er Zugriff auf das System</li>
                                <li>Der Einladungslink ist für begrenzte Zeit gültig (standardmäßig 7 Tage)</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Aktive Einladungen -->
                <div v-if="pendingInvitations.length > 0" class="rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm bg-white dark:bg-zinc-900 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <Clock class="h-5 w-5 text-amber-500" />
                            <h3 class="font-medium">Ausstehende Einladungen</h3>
                        </div>
                        <Badge variant="outline" class="bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                            {{ pendingInvitations.length }} ausstehend
                        </Badge>
                    </div>
                    
                    <div class="space-y-3">
                        <div v-for="invitation in pendingInvitations" :key="invitation.id" 
                             class="flex items-center justify-between p-3 rounded-lg border border-gray-100 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-800/50">
                            <div class="flex items-center gap-3">
                                <Mail class="h-4 w-4 text-gray-400" />
                                <div>
                                    <p class="text-sm font-medium">{{ invitation.email }}</p>
                                    <p class="text-xs text-gray-500">Eingeladen am {{ formatDate(invitation.created_at) }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <Badge variant="outline" class="text-xs">
                                    Läuft ab in {{ invitation.expires_in_days }} Tagen
                                </Badge>
                                <Button 
                                    variant="ghost" 
                                    size="sm"
                                    @click="resendInvitation(invitation.id)"
                                    :disabled="isResending === invitation.id"
                                >
                                    <template v-if="isResending === invitation.id">
                                        <LoaderCircle class="h-3 w-3 animate-spin" />
                                    </template>
                                    <template v-else>
                                        <RefreshCw class="h-3 w-3" />
                                    </template>
                                </Button>
                            </div>
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
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { 
    ChevronLeft, 
    MailPlus, 
    UserCog, 
    Send, 
    LoaderCircle,
    Info,
    Clock,
    Mail,
    RefreshCw
} from 'lucide-vue-next'; 
import { ref, reactive } from 'vue'; 
import { router } from '@inertiajs/vue3';

interface InvitationFormData {
    email: string;
    role: string;
    message: string;
    restrict_access: boolean;
    auto_verify: boolean;
    expiry_days: number;
}

interface FormErrors {
    email?: string;
    role?: string;
    message?: string;
    expiry_days?: string;
    [key: string]: string | undefined;
}

interface PendingInvitation {
    id: number;
    email: string;
    created_at: string;
    expires_in_days: number;
}

// Props für ausstehende Einladungen
interface Props {
    pendingInvitations?: PendingInvitation[];
}

const props = withDefaults(defineProps<Props>(), {
    pendingInvitations: () => []
});

// Formular-Daten
const form = reactive<InvitationFormData>({
    email: '',
    role: 'user',
    message: '',
    restrict_access: false,
    auto_verify: false,
    expiry_days: 7
});

// Formular-Fehler
const formErrors = ref<FormErrors>({});

// Ladezustände
const isSubmitting = ref(false);
const isResending = ref<number | null>(null);

// Zurück zur Übersicht
const goBack = () => {
    router.get('/admin/users');
};

// Formular zurücksetzen
const resetForm = () => {
    form.email = '';
    form.role = 'user';
    form.message = '';
    form.restrict_access = false;
    form.auto_verify = false;
    form.expiry_days = 7;
    formErrors.value = {};
};

// Datum formatieren
const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('de-DE', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

// Einladung erneut senden
const resendInvitation = async (invitationId: number) => {
    isResending.value = invitationId;
    
    try {
        await router.post(`/admin/users/invitations/${invitationId}/resend`, {}, {
            preserveScroll: true,
            onSuccess: () => {
                // Optional: Erfolgsmeldung anzeigen
                console.log('Einladung erneut gesendet');
            },
            onError: (error) => {
                console.error('Fehler beim erneuten Senden der Einladung:', error);
            },
            onFinish: () => {
                isResending.value = null;
            }
        });
    } catch (error) {
        console.error('Fehler:', error);
        isResending.value = null;
    }
};

// Einladung absenden
const submitInvitation = async () => {
    isSubmitting.value = true;
    formErrors.value = {};

    try {
        await router.post('/admin/users/invitations', form, {
            preserveScroll: true,
            onSuccess: () => {
                // Erfolgreich gesendet - weiterleiten zur Übersicht
                router.visit('/admin/users', {
                    only: ['users', 'filters'],
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => {
                        // Optional: Erfolgsmeldung anzeigen
                        console.log('Einladung erfolgreich gesendet');
                    }
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
        console.error('Fehler beim Senden der Einladung:', error);
        isSubmitting.value = false;
    }
};
</script>