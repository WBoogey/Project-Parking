import apiClient from "@/api/client";
import type { Rate } from "@/types/OwnerTypes";

export interface ParkingWithRates {
  id: string;
  location: string;
  capacity: number;
  rates: Rate[];
}

export const parkingApi = {
  getParkings: async (): Promise<ParkingWithRates[]> => {
    const response = await apiClient.get<{ data: ParkingWithRates[] }>(
      "/parkings",
    );
    return response.data.data;
  },

  getParkingById: async (id: string): Promise<ParkingWithRates | undefined> => {
    const response = await apiClient.get<{ data: ParkingWithRates[] }>(
      "/parkings",
    );
    return response.data.data.find((p) => p.id === id);
  },
};
