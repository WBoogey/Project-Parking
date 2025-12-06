import { Link } from "react-router";
import Button from "../atoms/Button";
import LinkDropdown from "../molecules/LinkDropdown/LinkDropdown";
import { searchDropdownData, ownerDropdownData } from "./Navbar.data";

const Navbar = () => {
  return (
    <nav className="flex items-center justify-between px-8 py-4 bg-primary border-b border-tertiary w-full">
      <Link to="/" className="text-xl font-semibold">
        AppName
      </Link>
      <div className="flex items-center gap-8">
        <LinkDropdown
          title={searchDropdownData.title}
          dropdownElements={searchDropdownData.elements}
        />
        <LinkDropdown
          title={ownerDropdownData.title}
          dropdownElements={ownerDropdownData.elements}
        />
      </div>
      <div className="flex items-center gap-4">
        <Button onClick={() => {}} variant="plain">
          S&apos;inscrire
        </Button>
        <Button onClick={() => {}} size="sm">
          Se connecter
        </Button>
      </div>
    </nav>
  );
};

export default Navbar;
