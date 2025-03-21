-- Tạo bảng Accounts
CREATE TABLE Accounts (
    id_account INT(11) PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role TINYINT NOT NULL DEFAULT 0,  
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Tạo bảng Contacts
CREATE TABLE Contacts (
    id_contact VARCHAR(20) PRIMARY KEY,
    content TEXT NOT NULL,
    status TINYINT NOT NULL DEFAULT 0,
    id_account INT(11),
    FOREIGN KEY (id_account) REFERENCES Accounts(id_account) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Tạo bảng Promotions
CREATE TABLE Promotions (
    id_promotion VARCHAR(20) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    start_day DATE NOT NULL,
    end_day DATE NOT NULL,
    discount_rate DECIMAL(5,2) NOT NULL CHECK (discount_rate >= 0 AND discount_rate <= 100),
    id_account INT(11) NOT NULL,
    FOREIGN KEY (id_account) REFERENCES Accounts(id_account) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Tạo bảng Products
CREATE TABLE Products (
    id_product VARCHAR(20) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    quantity INT NOT NULL DEFAULT 0,
    price DECIMAL(10,2) NOT NULL CHECK (price >= 0),
    delivery_limit INT NOT NULL,
    unit VARCHAR(50),
    id_promotion VARCHAR(20),
    FOREIGN KEY (id_promotion) REFERENCES Promotions(id_promotion) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Tạo bảng Image_Product
CREATE TABLE Image_Product (
    id_image VARCHAR(20) PRIMARY KEY,
    URL_image VARCHAR(255) NOT NULL,
    id_product VARCHAR(20) NOT NULL,
    FOREIGN KEY (id_product) REFERENCES Products(id_product) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Tạo bảng Product_Catalog
CREATE TABLE Product_Catalog (
    id_catalog VARCHAR(20) PRIMARY KEY,
    name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Tạo bảng Product_Catalog_details (Nhiều - Nhiều)
CREATE TABLE Product_Catalog_details (
    id_product VARCHAR(20),
    id_catalog VARCHAR(20),
    PRIMARY KEY (id_product, id_catalog),
    FOREIGN KEY (id_product) REFERENCES Products(id_product) ON DELETE CASCADE,
    FOREIGN KEY (id_catalog) REFERENCES Product_Catalog(id_catalog) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Tạo bảng Payments
-- CREATE TABLE Payments (
--     id_payment VARCHAR(20) PRIMARY KEY,
--     name VARCHAR(100) NOT NULL,
--     qr_code_url VARCHAR(255) NULL 
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE Orders (
    id_order VARCHAR(20) PRIMARY KEY,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_account INT(11) NOT NULL,
    id_delivery VARCHAR(20) NULL,
    -- status_payment VARCHAR(50) NOT NULL,
    -- id_payment VARCHAR(20) NOT NULL,
    -- FOREIGN KEY (id_payment) REFERENCES Payments(id_payment) ON DELETE CASCADE
    FOREIGN KEY (id_account) REFERENCES Accounts(id_account) ON DELETE CASCADE,
    FOREIGN KEY (id_delivery) REFERENCES delivery_information(id_delivery) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Tạo bảng Delivery_Information
CREATE TABLE Delivery_Information (
    id_delivery varchar(20) PRIMARY KEY ,
    id_account INT(11),
    house_number VARCHAR(50),
    ward VARCHAR(100),
    district VARCHAR(100),
    city VARCHAR(100),
    receiver_name VARCHAR(100),
    receiver_phone VARCHAR(20),
    FOREIGN KEY (id_account) REFERENCES Accounts(id_account) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Tạo bảng Order_details
CREATE TABLE Order_details (
    id_order VARCHAR(20),
    id_product VARCHAR(20),
    quantity INT NOT NULL DEFAULT 1,
    PRIMARY KEY (id_order, id_product),
    FOREIGN KEY (id_order) REFERENCES Orders(id_order) ON DELETE CASCADE,
    FOREIGN KEY (id_product) REFERENCES Products(id_product) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Tạo bảng Feedbacks
CREATE TABLE Feedbacks (
    id_feedback VARCHAR(20) PRIMARY KEY,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    id_product VARCHAR(20) NOT NULL,
    id_account INT(11) NOT NULL,
    FOREIGN KEY (id_product) REFERENCES Products(id_product) ON DELETE CASCADE,
    FOREIGN KEY (id_account) REFERENCES Accounts(id_account) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Tạo bảng Responses (phản hồi feedback)
CREATE TABLE Responses (
    id_responses VARCHAR(20) PRIMARY KEY,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    id_feedback VARCHAR(20) NOT NULL,
    id_account INT(11) NOT NULL,
    FOREIGN KEY (id_feedback) REFERENCES Feedbacks(id_feedback) ON DELETE CASCADE,
    FOREIGN KEY (id_account) REFERENCES Accounts(id_account) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Tạo bảng Suppliers
CREATE TABLE Suppliers (
    id_supplier VARCHAR(20) PRIMARY KEY,
    name VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Tạo bảng Product_receipt
CREATE TABLE Product_receipt (
    id_receipt VARCHAR(20) PRIMARY KEY,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_supplier VARCHAR(20) NOT NULL,
    FOREIGN KEY (id_supplier) REFERENCES Suppliers(id_supplier) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Tạo bảng Product_receipt_details
CREATE TABLE Product_receipt_details (
    id_receipt VARCHAR(20),
    id_product VARCHAR(20),
    quantity INT NOT NULL,
    purchase_price DECIMAL(10,2) NOT NULL CHECK (purchase_price >= 0),
    PRIMARY KEY (id_receipt, id_product),
    FOREIGN KEY (id_receipt) REFERENCES Product_receipt(id_receipt) ON DELETE CASCADE,
    FOREIGN KEY (id_product) REFERENCES Products(id_product) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
