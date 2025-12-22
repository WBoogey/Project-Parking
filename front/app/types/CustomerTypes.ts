export interface Reservation {
  id: string;
  startTime: string;
  endTime: string;
  status: string;
  parkingId: string;
  userId: string;
  rateId: string | null;
  amount: number | null;
  isFree: boolean;
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
  endTime: string | null;
  status: string;
  parkingId: string;
  userId: string;
}
