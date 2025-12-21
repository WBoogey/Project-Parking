export const queryKeys = {
  auth: {
    all: ["auth"] as const,
    user: () => [...queryKeys.auth.all, "user"] as const,
  },
  customer: {
    all: ["customer"] as const,
    reservations: () => [...queryKeys.customer.all, "reservations"] as const,
    subscriptions: () => [...queryKeys.customer.all, "subscriptions"] as const,
    stationings: () => [...queryKeys.customer.all, "stationings"] as const,
  },
  owner: {
    all: ["owner"] as const,
    parkings: () => [...queryKeys.owner.all, "parkings"] as const,
    rates: (parkingId: string) =>
      [...queryKeys.owner.all, "parkings", parkingId, "rates"] as const,
  },
  parking: {
    all: ["parking"] as const,
    list: (search?: string) =>
      [...queryKeys.parking.all, "list", search] as const,
    detail: (id: string) => [...queryKeys.parking.all, "detail", id] as const,
  },
  subscription: {
    all: ["subscription"] as const,
    list: () => [...queryKeys.subscription.all, "list"] as const,
    detail: (id: string) =>
      [...queryKeys.subscription.all, "detail", id] as const,
  },
};
