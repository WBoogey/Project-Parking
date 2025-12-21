export interface WeeklySlot {
  dayOfWeek: string;
  startHour: string;
  endHour: string;
}

export interface SubscriptionDetail {
  id: string;
  userId: string;
  parkingId: string;
  startDate: string;
  endDate: string;
  rate: number;
  weeklySlots: WeeklySlot[];
  status: string;
}

export interface CreateSubscriptionData {
  parkingId: string;
  rateId: string;
  startDate: string;
  endDate: string;
  weeklySlots: WeeklySlot[];
}
