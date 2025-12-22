import apiClient from "@/api/client";

export interface EnterParkingResponse {
  id: string;
  userId: string;
  parkingId: string;
  startTime: string;
  status: string;
}

export interface ExitParkingResponse {
  id: string;
  userId: string;
  parkingId: string;
  startTime: string;
  endTime: string;
  status: string;
  isFree: boolean;
  amount: number | null;
  checkoutUrl?: string;
  paymentStatus?: string;
}

export const stationingApi = {
  enterParking: async (parkingId: string): Promise<EnterParkingResponse> => {
    const response = await apiClient.post<{ data: EnterParkingResponse }>(
      "/stationings/enter",
      { parkingId },
    );
    return response.data.data;
  },

  exitParking: async (parkingId: string): Promise<ExitParkingResponse> => {
    const response = await apiClient.post<{ data: ExitParkingResponse }>(
      "/stationings/exit",
      { parkingId },
    );
    return response.data.data;
  },
};
