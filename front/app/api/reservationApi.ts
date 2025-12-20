export interface CreateReservationData {
  parkingId: string;
  startDate: string;
  endDate: string;
  paymentMethod: string;
}

export const reservationApi = {
  createReservation: async (data: CreateReservationData) => {
    await new Promise((resolve) => setTimeout(resolve, 1000));

    console.log("Reservation created (MOCK):", data);

    return {
      success: true,
      message: "Reservation confirmed",
      data: {
        id: "mock-res-" + Date.now(),
        ...data,
        status: "confirmed",
      },
    };
  },
};
