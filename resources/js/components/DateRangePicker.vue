<script setup lang="ts">
import type { DateRange } from 'reka-ui'
import {
  CalendarDate,
  DateFormatter,
  getLocalTimeZone,
} from '@internationalized/date'

import { CalendarIcon } from 'lucide-vue-next'
import { type Ref, ref } from 'vue'
import { cn } from '@/lib/utils'
import { Button } from '@/components/ui/button'
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'
import { RangeCalendar } from '@/components/ui/range-calendar'
import { watch } from 'vue'
import moment from 'moment'

const model = defineModel<{
  start: { year?: number, month?: number, day?: number },
  end: { year?: number, month?: number, day?: number },
}>()

const df = new DateFormatter('id-ID', {
  dateStyle: 'medium',
})

const now = moment()
const value = ref({
  start: new CalendarDate(model.value?.start.year ?? now.year(), model.value?.start.month ?? now.month(), model.value?.start.day ?? now.day()),
  end: new CalendarDate(model.value?.end.year ?? now.year(), model.value?.end.month ?? now.month(), model.value?.end.day ?? now.day()),
  // end: new CalendarDate(2022, 1, 20).add({ days: 20 }),
}) as Ref<DateRange>
watch(value, ({ start, end }) => {
  console.log({ start, end })
  model.value = {
    start: { year: start?.year, month: start?.month, day: start?.day },
    end: { year: end?.year, month: end?.month, day: end?.day }
  }
})
</script>
<template>
  <Popover>
    <PopoverTrigger as-child>
      <Button variant="outline" :class="cn(
        'w-[280px] justify-start text-left font-normal',
        !value && 'text-muted-foreground',
      )">
        <CalendarIcon class="mr-2 h-4 w-4" />
        <template v-if="value.start">
          <template v-if="value.end">
            {{ df.format(value.start.toDate(getLocalTimeZone())) }} - {{ df.format(value.end.toDate(getLocalTimeZone()))
            }}
          </template>

          <template v-else>
            {{ df.format(value.start.toDate(getLocalTimeZone())) }}
          </template>
        </template>
        <template v-else>
          Pick a date
        </template>
      </Button>
    </PopoverTrigger>
    <PopoverContent class="w-auto p-0">
      <RangeCalendar v-model="value" initial-focus :number-of-months="2"
        @update:start-value="(startDate) => value.start = startDate" />
    </PopoverContent>
  </Popover>
</template>