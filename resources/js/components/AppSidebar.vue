<script setup lang="ts">
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';

// Aucun logo ni liens inutiles, uniquement le slot pour les conversations
</script>

<template>
  <Sidebar collapsible="icon" variant="inset">
    <!-- Header minimal (optionnel, vide pour garder espace propre) -->
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg">
            <!-- Vide, ou tu peux mettre un logo perso plus tard -->
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>

    <!-- Menu principal -->
    <SidebarContent>
      <!-- Slot pour injecter le bouton Nouvelle conversation et la liste -->
      <slot name="sidebar-content" />
    </SidebarContent>

    <!-- Footer minimal (vide pour l'instant) -->
    <SidebarFooter>
      <SidebarMenu>
        <!-- Vide, aucun lien externe -->
      </SidebarMenu>
    </SidebarFooter>
  </Sidebar>
</template>
