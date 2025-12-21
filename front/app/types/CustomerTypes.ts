export interface Reservation {
  id: string;
  dayOfWeek: string;
  startHour: string;
  endHour: string;
  parkingId: string;
}

export interface Subscription {
  id: string;
  startDate: string;
  endDate: string;
  rate: {
    id: string;
    name: string;
    amount: number;
  };
  parkingId: string;
}

export interface Stationing {
  id: string;
  startTime: string;
  endTime: string;
  status: string;
  parkingId: string;
}
