export const queryKeys = {
  auth: {
    all: ["auth"] as const,
    user: () => [...queryKeys.auth.all, "user"] as const,
  },
  customer: {
    all: ["customer"] as const,
    reservations: () => [...queryKeys.customer.all, "reservations"] as const,
    subscriptions: () => [...queryKeys.customer.all, "subscriptions"] as const,
  },
  owner: {
    all: ["owner"] as const,
    parkings: () => [...queryKeys.owner.all, "parkings"] as const,
  },
};
