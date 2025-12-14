import type { DropdownFullListType } from "@/types/LinkDropdownTypes";

type NavbarDropdownData = {
  title: string;
  elements: DropdownFullListType;
};

export const searchDropdownData: NavbarDropdownData = {
  title: "Vous cherchez une place",
  elements: [
    [
      { title: "Trouver un parking", href: "/search" },
      { title: "Comment ça marche", href: "/#how-it-works" },
    ],
  ],
};

export const ownerDropdownData: NavbarDropdownData = {
  title: "Vous êtes propriétaire",
  elements: [
    [
      { title: "Tableau de bord", href: "/owner" },
      { title: "Ajouter un parking", href: "/owner/parkings/add" },
    ],
  ],
};
