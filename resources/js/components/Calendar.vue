<script setup lang="ts">
import "vue-simple-calendar/dist/vue-simple-calendar.css"
import "vue-simple-calendar/dist/css/default.css"

import { CalendarView, CalendarViewHeader, CalendarMath } from "vue-simple-calendar"
</script>
<template>
  <div class="calendar-parent">
    <calendar-view :items="items" :show-date="showDate" :time-format-options="{ hour: 'numeric', minute: '2-digit' }"
      :enable-drag-drop="true" :show-times="true" :date-classes="myDateClasses" :display-period-uom="'week'"
      :display-period-count="displayPeriodCount" :starting-day-of-week="0" :class="themeClasses"
      :period-changed-callback="periodChanged" :current-period-label="useTodayIcons ? 'icons' : ''"
      :displayWeekNumbers="false" :enable-date-selection="true" @drop-on-date="onDrop" @click-date="onClickDay"
      @click-item="onClickItem">
      <template #header="{ headerProps }">
        <calendar-view-header slot="header" :header-props="headerProps" @input="setShowDate" />
      </template>
    </calendar-view>
  </div>
</template>