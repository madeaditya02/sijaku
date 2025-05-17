import { BadgeVariants } from '@/components/ui/badge';
import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export const namaHari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"]

export const badgeType = (status: string): BadgeVariants['variant'] => {
    return status == 'Batal' ? 'destructive' : (status == 'Offline' ? 'default' : (status == 'Online' ? 'secondary' : 'pending'))
}
