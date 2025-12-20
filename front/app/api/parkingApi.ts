const MOCK_PARKINGS = [
  {
    id: "1",
    location: "12 Rue de la Paix, 75000 Paris",
    capacity: 15,
    available: 3,
    price: 15,
    priceDisplay: "15€ / jour",
    description:
      "Parking sécurisé en plein centre de Paris. Accès 24/7, vidéosurveillance.",
    features: ["Sécurisé", "Couvert", "Accès handicapé", "Bornes électriques"],
    pricing: {
      hourly: 2.5,
      daily: 15,
      monthly: 150,
    },
  },
  {
    id: "2",
    location: "45 Avenue des Champs-Élysées, 75008 Paris",
    capacity: 50,
    available: 12,
    price: 20,
    priceDisplay: "20€ / jour",
    description: "Parking spacieux à deux pas des Champs-Élysées.",
    features: ["Sécurisé", "Couvert", "Voiturier"],
    pricing: {
      hourly: 3.5,
      daily: 20,
      monthly: 200,
    },
  },
  {
    id: "3",
    location: "8 Boulevard Haussmann, 75009 Paris",
    capacity: 30,
    available: 0,
    price: 18,
    priceDisplay: "18€ / jour",
    description: "Idéal pour le shopping aux Galeries Lafayette.",
    features: ["Sécurisé", "Accès handicapé"],
    pricing: {
      hourly: 3.0,
      daily: 18,
      monthly: 180,
    },
  },
];

export interface ParkingDetail {
  id: string;
  location: string;
  capacity: number;
  available: number;
  price: number;
  priceDisplay: string;
  description: string;
  features: string[];
  pricing: {
    hourly: number;
    daily: number;
    monthly: number;
  };
}

export const parkingApi = {
  getParkings: async (search?: string): Promise<ParkingDetail[]> => {
    await new Promise((resolve) => setTimeout(resolve, 500));

    if (!search) return MOCK_PARKINGS;

    const lowerSearch = search.toLowerCase();
    return MOCK_PARKINGS.filter((p) =>
      p.location.toLowerCase().includes(lowerSearch),
    );
  },

  getParkingById: async (id: string): Promise<ParkingDetail | undefined> => {
    await new Promise((resolve) => setTimeout(resolve, 500));

    return MOCK_PARKINGS.find((p) => p.id === id);
  },
};
