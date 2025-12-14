export type SeatStatus = "free" | "occupied";

export type ReservationType = "daily" | "monthly";

export interface TimeRange {
  start: string;
  end: string;
}

export interface SeatProCardData {
  name: string;
  status: SeatStatus;
  limitReached?: boolean;
  occupiedBy?: string;
  reservationType?: ReservationType;
  timeRange?: TimeRange;
}
