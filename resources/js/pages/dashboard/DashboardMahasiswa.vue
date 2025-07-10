<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import "vue-simple-calendar/dist/vue-simple-calendar.css"
import "vue-simple-calendar/dist/css/default.css"
import moment from "moment";
import { CalendarView, CalendarViewHeader, ICalendarItem } from "vue-simple-calendar"
import { ref } from 'vue';
import Table from '@/components/ui/table/Table.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import TableCell from '@/components/ui/table/TableCell.vue';
import { TableHead, TableHeader } from '@/components/ui/table';
import { Perkuliahan } from '@/types/model';
import Badge from '@/components/ui/badge/Badge.vue';
import { badgeType } from '@/lib/utils';
const props = defineProps<{
	kuliahSemester: Perkuliahan[],
	kuliahHariIni: Perkuliahan[],
}>()
const page = usePage<{ auth: { user: any } }>()
const periodChanged = () => {
	// console.log('Period changed')
}
const onClickDay = (day: any) => {
	console.log(day)
}
const onDrop = () => { }
const onClickItem = (item: any) => {
	console.log(item)
}

const items: ICalendarItem[] = props.kuliahSemester.filter(kuliah => kuliah.status == 'Offline' || kuliah.status == 'Online').map(kuliah => ({
	id: kuliah.id_kuliah,
	startDate: moment(kuliah.waktu_mulai).toDate(),
	classes: kuliah.status == 'Offline' ? ["active-blue"] : [],
	// endDate: moment(kuliah.waktu_selesai).startOf.toDate(),
	title: kuliah.mata_kuliah.nama_matkul,
}))
console.log(items);

// const items: ICalendarItem[] = [
// 	{
// 		id: "e3",
// 		startDate: moment('2025-06-20').toDate(),
// 		endDate: moment('2025-06-20').toDate(),
// 		title: "My Event",
// 	},
// ]
const showDate = ref(moment().toDate())
const setShowDate = (d: Date) => {
	console.log(`Changing calendar view to ${d.toLocaleDateString()}`)
	showDate.value = d
}
</script>
<template>

	<Head title="Dashboard" />

	<AppLayout>
		<div class="bg-white px-10 py-5 rounded-xl border border-[#bdbdbd] mb-6">
			<h1 class="text-2xl font-medium">Selamat Datang, {{ page.props.auth.user.mahasiswa.nama }}</h1>
		</div>
		<div class="calendar-parent">
			<calendar-view :items="items" :show-date="showDate" :time-format-options="{ hour: 'numeric', minute: '2-digit' }"
				:enable-drag-drop="true" :show-times="true" :display-period-uom="'month'" :display-period-count="1"
				:starting-day-of-week="0" :class="'theme-default'" :period-changed-callback="periodChanged"
				:current-period-label="'icons'" :displayWeekNumbers="false" :enable-date-selection="true" @drop-on-date="onDrop"
				@click-date="onClickDay" @click-item="onClickItem">
				<template #header="{ headerProps }">
					<calendar-view-header :header-props="headerProps" @input="setShowDate" />
				</template>
			</calendar-view>
		</div>
		<h2 class="text-2xl font-medium mt-10 mb-6">Jadwal Hari Ini</h2>
		<div class="rounded-xl border border-[#bdbdbd] bg-white jadwal-today" v-if="kuliahHariIni.length > 0">
			<Table>
				<TableHeader>
					<TableRow>
						<TableHead class="w-[100px]">
							Mata Kuliah
						</TableHead>
						<TableHead>Jadwal</TableHead>
						<TableHead>Status</TableHead>
						<TableHead>Dosen</TableHead>
						<TableHead>Ruangan</TableHead>
					</TableRow>
				</TableHeader>
				<TableBody>
					<TableRow v-for="kuliah in kuliahHariIni" :key="kuliah.id_kuliah">
						<TableCell class="font-medium">
							{{ kuliah.mata_kuliah.nama_matkul }}
						</TableCell>
						<TableCell>{{ kuliah.waktu_mulai_string }} - {{ kuliah.waktu_selesai_string }}</TableCell>
						<TableCell>
							<Badge :variant="badgeType(kuliah.status)">{{ kuliah.status }}</Badge>
						</TableCell>
						<TableCell>
							{{ kuliah.mata_kuliah.dosen?.nama }}
						</TableCell>
						<TableCell>
							{{ kuliah.ruangan }}
						</TableCell>
					</TableRow>
				</TableBody>
			</Table>
		</div>
		<h2 class="text-2xl font-medium text-center" v-else>Tidak ada perkuliahan hari ini</h2>
	</AppLayout>
</template>
<style>
.jadwal-today td,
.jadwal-today th {
	padding: 16px 24px;
}

.cv-item.active-blue {
	background-color: #D8EFFF;
}
</style>