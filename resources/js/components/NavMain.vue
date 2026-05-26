<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

defineProps<{
    items: NavItem[];
}>();

const { isCurrentUrl } = useCurrentUrl();
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel class="text-[10px] font-bold uppercase tracking-widest text-sidebar-foreground/55">
            Menú principal
        </SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton
                    as-child
                    :is-active="isCurrentUrl(item.href)"
                    :tooltip="item.title"
                    class="rounded-xl transition-colors data-[active=true]:bg-gradient-to-r data-[active=true]:from-cyan-500 data-[active=true]:to-teal-600 data-[active=true]:text-white data-[active=true]:shadow-lg data-[active=true]:shadow-cyan-500/30 hover:bg-sidebar-accent"
                >
                    <Link :href="item.href">
                        <component :is="item.icon" class="size-4" />
                        <span class="font-medium">{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
