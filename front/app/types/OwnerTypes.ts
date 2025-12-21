export interface Rate {
  id: string;
  parkingId: string;
  name: string;
  duration: number;
  amount: number;
}

export interface CreateRateData {
  name: string;
  duration: number;
  amount: number;
}
